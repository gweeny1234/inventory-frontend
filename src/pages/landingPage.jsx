import { Link } from "react-router-dom";
import "./landingPage.css";

const DIVIDER_BARS = Array.from({ length: 46 }).map((_, i) => ({
  height: Math.round(Math.abs(Math.sin(i * 0.55)) * 34 + 10),
  amber: i % 7 === 0,
}));

export default function LandingPage() {
  return (
    <div className="landing">

      {/* Navbar */}
      <nav className="navbar">
        <h2>Inventory</h2>

        <div className="nav-links">
          <a href="#features">Features</a>
          <a href="#about">About</a>

          <Link to="/login" className="login-btn">
            Log In
          </Link>
        </div>
      </nav>

      {/* Hero */}
      <section className="hero">
        <div className="hero-copy">

          <span className="hero-eyebrow">
            ● Live Stock Tracking
          </span>

          <h1>Know what's in stock, before it runs out.</h1>

          <p>
            Manage products, categories, suppliers, and orders
            with one modern dashboard built to help businesses
            monitor inventory accurately and efficiently.
          </p>

          <div className="hero-buttons">
            <Link to="/login" className="primary-btn">
              Log In
            </Link>
          </div>

        </div>
      </section>

      {/* Divider */}
      <div className="stock-divider" aria-hidden="true">
        {DIVIDER_BARS.map((bar, i) => (
          <span
            key={i}
            className={`bar${bar.amber ? " amber" : ""}`}
            style={{ height: `${bar.height}px` }}
          ></span>
        ))}
      </div>

      {/* Features */}
      <section id="features" className="features-wrap">

        <div className="features-intro">
          <span className="features-eyebrow">
            What you get
          </span>

          <h2>Everything the stockroom needs</h2>

          <p>
            Four tools that cover the full inventory process,
            from product management to order fulfillment.
          </p>
        </div>

        <div className="features">

          <div className="card">
            <h3>📦 Products</h3>
            <p>Track every product and its stock level.</p>
          </div>

          <div className="card">
            <h3>🗂 Categories</h3>
            <p>Organize products into categories.</p>
          </div>

          <div className="card">
            <h3>🚚 Suppliers</h3>
            <p>Manage supplier information and restocking.</p>
          </div>

          <div className="card">
            <h3>🛒 Orders</h3>
            <p>Monitor customer orders in real time.</p>
          </div>

        </div>

      </section>

      {/* Footer */}
      <footer>
        © 2026 Jen's Inventory
        <span className="build-tag">
          Inventory Management System
        </span>
      </footer>

    </div>
  );
}