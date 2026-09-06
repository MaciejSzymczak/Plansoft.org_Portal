document.addEventListener("DOMContentLoaded", function () {
  var header = document.querySelector(".site-header");
  var toggle = document.querySelector(".nav-toggle");
  var navLinks = document.querySelectorAll(".nav-links a");

  function onScroll() {
    if (window.scrollY > 12) header.classList.add("is-scrolled");
    else header.classList.remove("is-scrolled");
  }
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  if (toggle) {
    toggle.addEventListener("click", function () {
      header.classList.toggle("nav-open");
    });
  }

  navLinks.forEach(function (link) {
    link.addEventListener("click", function () {
      header.classList.remove("nav-open");
    });
  });

  var sections = Array.prototype.slice.call(document.querySelectorAll("main section[id]"));
  var spy = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        var link = document.querySelector('.nav-links a[href="#' + entry.target.id + '"]');
        if (!link) return;
        if (entry.isIntersecting) {
          navLinks.forEach(function (l) { l.classList.remove("active"); });
          link.classList.add("active");
        }
      });
    },
    { rootMargin: "-45% 0px -50% 0px" }
  );
  sections.forEach(function (s) { spy.observe(s); });
});
