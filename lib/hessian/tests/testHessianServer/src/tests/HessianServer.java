package tests;

import org.mortbay.jetty.Connector;
import org.mortbay.jetty.Server;
import org.mortbay.jetty.nio.SelectChannelConnector;
import org.mortbay.jetty.servlet.ServletHandler;
import org.mortbay.jetty.servlet.ServletHolder;

/**
 * @author andrey.kuprishov
 */
public final class HessianServer {

  public static void main(String[] args) {
    if (args.length != 2) {
      System.out.println("Usage: <host to bind> <port to bind>");
      System.out.println("Example: 127.0.0.1 8080");
      return;
    }

    final Server server = new Server();

    final Connector connector = new SelectChannelConnector();
    connector.setHost(args[0]);
    connector.setPort(Integer.parseInt(args[1]));
    server.addConnector(connector);

    ServletHandler handler = new ServletHandler();

    TestServlet servlet = new TestServlet();
    handler.addServletWithMapping(new ServletHolder(servlet), "/tests");
    
    server.addHandler(handler);
    try {
      server.start();
    } catch (Exception e) {
      e.printStackTrace();
    }
  }

}
