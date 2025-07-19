document.addEventListener("DOMContentLoaded", function () {
  const toggleButton = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector("aside");

  toggleButton?.addEventListener("click", function () {
    sidebar.classList.toggle("show");
  });
});
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("sidebarToggle");
  const sidebar = document.getElementById("sidebarMenu");
  const main = document.querySelector("main");

  toggleBtn?.addEventListener("click", function () {
    sidebar.classList.toggle("hide");
    main.classList.toggle("full");
  });
});
