// Port.js — front‑end behaviour for the contact page
const ACTION_URL = "http://localhost/portfolio/backend/contact.php";

// Wrap everything so the code runs **after** the DOM is ready
window.addEventListener("DOMContentLoaded", () => {
  // Burger menu toggle
  const burger = document.querySelector(".burger");
  const navLinks = document.querySelector(".navbar");
  burger?.addEventListener("click", () => navLinks.classList.toggle("open"));

  // Contact form logic
  const form = document.querySelector(".contact__form");
  if (!form) return;

  form.action = ACTION_URL;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const data = new FormData(form);

    if (!data.get("name") || !data.get("email") || !data.get("comments")) {
      alert("Please fill in all fields before submitting.");
      return;
    }

    try {
      const res = await fetch(ACTION_URL, { method: "POST", body: data });
      const json = await res.json();

      if (json.ok) {
        alert("✅ Thank you! Your message has been recorded.");
        form.reset();
      } else {
        alert("❌ Something went wrong. Please try again.");
      }
    } catch (err) {
      console.error("fetch error:", err);
      alert("❌ Network error. Please try again later.");
    }
  });

  // Dynamic footer year
  const yrSpan = document.getElementById("yr");
  if (yrSpan) yrSpan.textContent = String(new Date().getFullYear());
});
;5