const Ziggy = {"url":"http:\/\/127.0.0.1:8000","port":8000,"defaults":{},"routes":{"login":{"uri":"login","methods":["GET","HEAD"]},"auth.login":{"uri":"login","methods":["POST"]},"auth.logout":{"uri":"logout","methods":["POST"]},"books.file":{"uri":"books\/{book}\/file","methods":["POST"],"parameters":["book"],"bindings":{"book":"id"}},"books.advanced-search":{"uri":"advanced-search","methods":["POST"]},"books.index":{"uri":"books","methods":["GET","HEAD"]},"books.store":{"uri":"books","methods":["POST"]},"books.show":{"uri":"books\/{book}","methods":["GET","HEAD"],"parameters":["book"],"bindings":{"book":"id"}},"books.update":{"uri":"books\/{book}","methods":["PUT","PATCH"],"parameters":["book"],"bindings":{"book":"id"}},"books.destroy":{"uri":"books\/{book}","methods":["DELETE"],"parameters":["book"],"bindings":{"book":"id"}},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
