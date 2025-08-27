const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"logout":{"uri":"user\/logout","methods":["POST"]},"login":{"uri":"login","methods":["GET","HEAD"]},"auth":{"uri":"user\/login","methods":["POST"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
