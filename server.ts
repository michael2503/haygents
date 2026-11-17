import express from 'express';
import http from 'http';
import path from 'path';
import { fileURLToPath } from 'url';
import { spawn } from 'child_process';
import { createServer as createViteServer } from 'vite';
import { WebSocketServer, WebSocket as WsSocket } from 'ws';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const PORT = Number(process.env.PORT) || 3000;
const LARAVEL_PORT = 8085;
const LARAVEL_HOST = '127.0.0.1';

let laravelProcess: any = null;
let isStartingLaravel = false;

// Ensure PHP Artisan Serve is active
function ensureLaravelServer() {
  if (isStartingLaravel || laravelProcess) return;

  const req = http.get(`http://${LARAVEL_HOST}:${LARAVEL_PORT}`, (res) => {
    // Already running
    res.resume();
  });

  req.on('error', () => {
    if (isStartingLaravel) return;
    isStartingLaravel = true;
    console.log(`[EduTenant Proxy] Checking and starting Laravel server on port ${LARAVEL_PORT}...`);

    try {
      const artisan = spawn('php', ['artisan', 'serve', `--host=${LARAVEL_HOST}`, `--port=${LARAVEL_PORT}`], {
        cwd: path.resolve(__dirname, 'laravel_app'),
        stdio: 'pipe',
      });

      laravelProcess = artisan;

      artisan.stdout?.on('data', (data) => {
        const msg = data.toString();
        if (msg.includes('Server running on')) {
          console.log(`[EduTenant Proxy] ${msg.trim()}`);
          isStartingLaravel = false;
        }
      });

      artisan.stderr?.on('data', (data) => {
        console.error(`[EduTenant Proxy stderr]: ${data.toString().trim()}`);
      });

      artisan.on('error', (err) => {
        console.warn(`[EduTenant Proxy] PHP artisan could not be spawned: ${err.message}`);
        laravelProcess = null;
        isStartingLaravel = false;
      });

      artisan.on('exit', (code) => {
        console.log(`[EduTenant Proxy] Laravel process exited with code ${code}`);
        laravelProcess = null;
        isStartingLaravel = false;
      });
    } catch (err: any) {
      console.warn('[EduTenant Proxy] Failed to start PHP server:', err.message);
      laravelProcess = null;
      isStartingLaravel = false;
    }
  });
}

ensureLaravelServer();

// Periodic background check for scheduled monthly payroll automation
setInterval(() => {
  try {
    const check = spawn('php', ['artisan', 'payroll:auto-run'], {
      cwd: path.resolve(__dirname, 'laravel_app'),
      stdio: 'ignore',
    });
    check.on('error', () => {});
  } catch {}
}, 60000);

const app = express();

const LARAVEL_ROUTES = [
  '/dashboard',
  '/students',
  '/branches',
  '/staff',
  '/staff-attendance',
  '/payroll',
  '/classes',
  '/subjects',
  '/clubs',
  '/sport-houses',
  '/parents',
  '/parent',
  '/student-attendance',
  '/timetable',
  '/results',
  '/school-fees',
  '/id-cards',
  '/incomes',
  '/expenses',
  '/transactions',
  '/cbt',
  '/library',
  '/settings',
  '/subscription',
  '/superadmin',
  '/login',
  '/logout',
  '/demo-login',
  '/register',
  '/register-school',
  '/homework',
  '/syllabus',
  '/tests',
  '/practicals',
  '/hostel',
  '/school-bus',
  '/messages',
  '/password',
  '/forgot-password',
  '/reset-password',
  '/verify-email',
  '/scheme-of-work',
  '/lecture-notes',
  '/teacher',
  '/student',
  '/exams',
  '/api',
  '/sanctum',
];

function isLaravelRoute(urlPath: string): boolean {
  if (urlPath.startsWith('/live')) return true;
  for (const prefix of LARAVEL_ROUTES) {
    if (urlPath === prefix || urlPath.startsWith(`${prefix}/`) || urlPath.startsWith(`${prefix}?`)) {
      return true;
    }
  }
  return false;
}

function proxyToLaravel(req: express.Request, res: express.Response) {
  let targetPath = req.originalUrl || req.url;
  if (targetPath.startsWith('/live')) {
    targetPath = targetPath.replace(/^\/live/, '') || '/';
  }

  const hostHeader = req.get('host') || `localhost:${PORT}`;

  const headers: http.OutgoingHttpHeaders = {
    ...req.headers,
    host: `${LARAVEL_HOST}:${LARAVEL_PORT}`,
    'x-forwarded-for': req.ip,
    'x-forwarded-proto': req.protocol,
    'x-forwarded-host': hostHeader,
  };

  const options: http.RequestOptions = {
    hostname: LARAVEL_HOST,
    port: LARAVEL_PORT,
    path: targetPath,
    method: req.method,
    headers,
  };

  const proxyReq = http.request(options, (proxyRes) => {
    // Rewrite redirects pointing to 127.0.0.1:8085 to relative URLs
    const location = proxyRes.headers['location'];
    if (location) {
      proxyRes.headers['location'] = location.replace(/^http:\/\/127\.0\.0\.1:8085/, '');
    }

    res.writeHead(proxyRes.statusCode || 200, proxyRes.headers);
    proxyRes.pipe(res, { end: true });
  });

  proxyReq.on('error', (err) => {
    console.error('[EduTenant Proxy] Connection error to Laravel:', err.message);
    ensureLaravelServer();
    res.status(502).send(`
      <!DOCTYPE html>
      <html>
      <head><title>EduTenant Server Starting...</title>
      <meta http-equiv="refresh" content="2">
      <style>body{background:#0F172A;color:white;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;text-align:center;}</style>
      </head>
      <body>
        <div>
          <h2>🚀 Starting EduTenant Laravel 10 Engine...</h2>
          <p style="color:#94A3B8;">Connecting to local PHP server on port ${LARAVEL_PORT}. Auto-refreshing in 2 seconds.</p>
        </div>
      </body>
      </html>
    `);
  });

  req.pipe(proxyReq, { end: true });
}

// WebSockets & Real-Time Communications Registry
interface ConnectedClient {
  ws: WsSocket;
  userId?: number;
  schoolId?: number;
  branchId?: number;
  role?: string;
  name?: string;
  conversationId?: number;
  callId?: number;
  isAlive?: boolean;
}

const activeSockets = new Set<ConnectedClient>();

function broadcastToClients(predicate: (client: ConnectedClient) => boolean, payload: any) {
  const messageStr = JSON.stringify(payload);
  for (const client of activeSockets) {
    if (client.ws.readyState === WsSocket.OPEN && predicate(client)) {
      try {
        client.ws.send(messageStr);
      } catch (err) {
        console.warn('[WS Broadcast Error]', err);
      }
    }
  }
}

// Internal Broadcast Endpoint for Laravel Events
app.post('/api/internal/broadcast', express.json(), (req, res) => {
  const { event, channel, data } = req.body || {};

  if (!event || !channel) {
    return res.status(400).json({ error: 'Missing event or channel' });
  }

  // Route broadcast based on channel prefix
  if (channel.startsWith('conversation_')) {
    const convId = Number(channel.replace('conversation_', ''));
    broadcastToClients(
      (c) => c.conversationId === convId || (data?.recipient_ids && Array.isArray(data.recipient_ids) && c.userId && data.recipient_ids.includes(c.userId)),
      { event, channel, data }
    );
  } else if (channel.startsWith('user_')) {
    const targetUserId = Number(channel.replace('user_', ''));
    broadcastToClients((c) => c.userId === targetUserId, { event, channel, data });
  } else if (channel.startsWith('call_')) {
    const callId = Number(channel.replace('call_', ''));
    broadcastToClients((c) => c.callId === callId, { event, channel, data });
  } else if (channel.startsWith('school_')) {
    const schoolId = Number(channel.replace('school_', ''));
    broadcastToClients((c) => c.schoolId === schoolId, { event, channel, data });
  } else {
    // Broadcast to all
    broadcastToClients(() => true, { event, channel, data });
  }

  res.json({ success: true, delivered_to: activeSockets.size });
});

// Real-Time Socket Health & Diagnostic Endpoint
app.get('/api/internal/socket-status', (req, res) => {
  res.json({
    status: 'online',
    active_connections: activeSockets.size,
    uptime_seconds: Math.floor(process.uptime()),
    port: PORT,
    timestamp: new Date().toISOString(),
  });
});

// Check for proxy routes before Vite or static handlers
app.use((req, res, next) => {
  const urlPath = req.path;

  // Direct native view mode trigger
  if (urlPath === '/' && req.query.view === 'native') {
    req.url = '/login';
    return proxyToLaravel(req, res);
  }

  if (urlPath === '/api/internal/broadcast') {
    return next();
  }

  if (isLaravelRoute(urlPath)) {
    return proxyToLaravel(req, res);
  }

  next();
});

// Setup Vite in development or serve static in production
async function startServer() {
  const isDev = process.env.NODE_ENV !== 'production';

  if (isDev) {
    const vite = await createViteServer({
      server: {
        middlewareMode: true,
        hmr: process.env.DISABLE_HMR !== 'true',
        watch: process.env.DISABLE_HMR === 'true' ? null : {},
      },
      appType: 'spa',
    });

    app.use(vite.middlewares);
  } else {
    app.use(express.static(path.resolve(__dirname, 'dist')));
    app.get('*', (_req, res) => {
      res.sendFile(path.resolve(__dirname, 'dist', 'index.html'));
    });
  }

  const server = http.createServer(app);

  // Setup WebSocket Server for Real-Time WhatsApp Chat, Notifications & Group Calls
  const wss = new WebSocketServer({ server, path: '/ws/chat' });

  wss.on('connection', (ws: WsSocket) => {
    const client: ConnectedClient = { ws, isAlive: true };
    activeSockets.add(client);

    ws.on('pong', () => {
      client.isAlive = true;
    });

    ws.on('message', (messageRaw: any) => {
      try {
        const msg = JSON.parse(messageRaw.toString());
        switch (msg.type) {
          case 'ping':
            client.isAlive = true;
            ws.send(JSON.stringify({ type: 'pong', timestamp: Date.now() }));
            break;

          case 'pong':
            client.isAlive = true;
            break;

          case 'auth':
            client.userId = Number(msg.userId);
            client.schoolId = Number(msg.schoolId);
            client.branchId = Number(msg.branchId);
            client.role = msg.role;
            client.name = msg.name;
            ws.send(JSON.stringify({ event: 'authenticated', userId: client.userId }));
            break;

          case 'join_conversation':
            client.conversationId = Number(msg.conversationId);
            break;

          case 'leave_conversation':
            if (client.conversationId === Number(msg.conversationId)) {
              client.conversationId = undefined;
            }
            break;

          case 'join_call':
            client.callId = Number(msg.callId);
            // Notify other participants in the call room
            broadcastToClients(
              (c) => c !== client && c.callId === client.callId,
              {
                event: 'user_joined_call',
                callId: client.callId,
                user: { id: client.userId, name: client.name, role: client.role },
              }
            );
            break;

          case 'leave_call':
            if (client.callId) {
              const previousCallId = client.callId;
              client.callId = undefined;
              broadcastToClients(
                (c) => c !== client && c.callId === previousCallId,
                {
                  event: 'user_left_call',
                  callId: previousCallId,
                  userId: client.userId,
                  name: client.name,
                }
              );
            }
            break;

          case 'call_signal':
            // WebRTC peer signaling (offer, answer, ICE candidate)
            broadcastToClients(
              (c) => c !== client && c.callId === Number(msg.callId) && (!msg.targetUserId || c.userId === Number(msg.targetUserId)),
              {
                event: 'call_signal',
                callId: Number(msg.callId),
                fromUserId: client.userId,
                fromName: client.name,
                targetUserId: msg.targetUserId,
                signal: msg.signal,
              }
            );
            break;

          case 'call_state_change':
            // Mute / video toggle / screen share in call
            broadcastToClients(
              (c) => c !== client && c.callId === Number(msg.callId),
              {
                event: 'call_state_change',
                callId: Number(msg.callId),
                userId: client.userId,
                isMuted: msg.isMuted,
                isVideoOn: msg.isVideoOn,
                isScreenSharing: msg.isScreenSharing,
              }
            );
            break;

          case 'typing':
            broadcastToClients(
              (c) => c !== client && c.conversationId === Number(msg.conversationId),
              {
                event: 'typing',
                conversationId: Number(msg.conversationId),
                userId: client.userId,
                name: client.name,
                isTyping: !!msg.isTyping,
              }
            );
            break;

          case 'seen_message':
            broadcastToClients(
              (c) => c !== client && c.conversationId === Number(msg.conversationId),
              {
                event: 'message_seen',
                conversationId: Number(msg.conversationId),
                messageId: msg.messageId,
                seenBy: client.userId,
              }
            );
            break;

          default:
            break;
        }
      } catch (err) {
        console.warn('[WS Message Error]', err);
      }
    });

    ws.on('close', () => {
      if (client.callId) {
        broadcastToClients(
          (c) => c !== client && c.callId === client.callId,
          {
            event: 'user_left_call',
            callId: client.callId,
            userId: client.userId,
            name: client.name,
          }
        );
      }
      activeSockets.delete(client);
    });

    ws.on('error', () => {
      activeSockets.delete(client);
    });
  });

  // Keep-alive heartbeat interval every 25 seconds
  // Sends ping frames to prevent proxies, Cloudflare, and firewalls from dropping idle WebSockets
  const heartbeatInterval = setInterval(() => {
    for (const client of activeSockets) {
      if (client.ws.readyState !== WsSocket.OPEN) {
        activeSockets.delete(client);
        continue;
      }
      if (client.isAlive === false) {
        try {
          client.ws.terminate();
        } catch {}
        activeSockets.delete(client);
        continue;
      }
      client.isAlive = false;
      try {
        client.ws.ping();
        client.ws.send(JSON.stringify({ type: 'server_ping', timestamp: Date.now() }));
      } catch {
        activeSockets.delete(client);
      }
    }
  }, 25000);

  server.on('close', () => {
    clearInterval(heartbeatInterval);
  });

  server.listen(PORT, '0.0.0.0', () => {
    console.log(`EduTenant Multi-School Platform running on http://0.0.0.0:${PORT}`);
  });
}

startServer().catch((err) => {
  console.error('Failed to start EduTenant server:', err);
  process.exit(1);
});
