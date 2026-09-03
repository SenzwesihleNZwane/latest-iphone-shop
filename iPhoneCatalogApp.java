import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpHandler;
import com.sun.net.httpserver.HttpServer;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.net.InetSocketAddress;
import java.net.URLDecoder;
import java.nio.charset.StandardCharsets;
import java.util.Locale;

public class iPhoneCatalogApp {

    private static final int PORT = 8080;

    public static void main(String[] args) throws IOException {
        HttpServer server = HttpServer.create(new InetSocketAddress(PORT), 0);

        // Print exactly where this server thinks its files live. If "images"
        // isn't listed underneath this path, that's the whole problem right there.
        File cwd = new File(".").getAbsoluteFile();
        System.out.println("Serving files from: " + cwd);
        File imagesDir = new File(cwd, "images");
        System.out.println("Looking for images in: " + imagesDir + " (exists: " + imagesDir.exists() + ")");

        server.createContext("/", new StaticFileHandler());

        server.setExecutor(null);
        System.out.println("==================================================");
        System.out.println("  iPhone Catalog Server running at:");
        System.out.println("  http://localhost:" + PORT);
        System.out.println("==================================================");
        server.start();
    }

    static class StaticFileHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            // getPath() already URL-decodes %20 etc., but decode again defensively
            // in case a client double-encodes spaces/special characters.
            String requestPath = exchange.getRequestURI().getPath();
            requestPath = URLDecoder.decode(requestPath, StandardCharsets.UTF_8);

            if (requestPath.equals("/")) {
                requestPath = "/index.html";
            }

            File file = new File("." + requestPath);

            if (!file.exists() || file.isDirectory()) {
                // This is the line that tells you EXACTLY what's wrong: it prints
                // the full absolute path the server looked for. Compare that,
                // character for character, against the real file on disk.
                System.out.println("404 NOT FOUND -> requested \"" + requestPath
                        + "\" -> looked for \"" + file.getAbsolutePath() + "\"");

                String response = "404 (Not Found): " + requestPath;
                byte[] bytes = response.getBytes(StandardCharsets.UTF_8);
                exchange.sendResponseHeaders(404, bytes.length);
                OutputStream os = exchange.getResponseBody();
                os.write(bytes);
                os.close();
                return;
            }

            String lower = requestPath.toLowerCase(Locale.ROOT);
            String contentType = "application/octet-stream";
            if (lower.endsWith(".html")) contentType = "text/html";
            else if (lower.endsWith(".css")) contentType = "text/css";
            else if (lower.endsWith(".js")) contentType = "text/javascript";
            else if (lower.endsWith(".jpeg") || lower.endsWith(".jpg")) contentType = "image/jpeg";
            else if (lower.endsWith(".png")) contentType = "image/png";
            else if (lower.endsWith(".webp")) contentType = "image/webp";
            else if (lower.endsWith(".svg")) contentType = "image/svg+xml";
            else if (lower.endsWith(".gif")) contentType = "image/gif";
            else if (lower.endsWith(".ico")) contentType = "image/x-icon";

            exchange.getResponseHeaders().set("Content-Type", contentType);
            exchange.sendResponseHeaders(200, file.length());

            try (OutputStream os = exchange.getResponseBody();
                 FileInputStream fs = new FileInputStream(file)) {
                byte[] buffer = new byte[4096];
                int count;
                while ((count = fs.read(buffer)) >= 0) {
                    os.write(buffer, 0, count);
                }
            }
        }
    }
}