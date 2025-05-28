function togglePopup() {
  const popup = document.getElementById("popup");
  const isHidden = window.getComputedStyle(popup).display === "none";

  popup.style.display = isHidden ? "block" : "none";
}