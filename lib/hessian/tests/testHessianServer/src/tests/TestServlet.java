package tests;

import com.caucho.hessian.server.HessianServlet;

/**
 * @author andrey.kuprishov
 */
public class TestServlet extends HessianServlet implements Test {
  public int getSum(int a, int b) {
    return a + b;
  }
}
