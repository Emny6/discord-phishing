document.getElementById('mobile-menu').addEventListener('click', function() {
  document.getElementById('menu-popup').classList.toggle('hidden');
});

function closePopup() {
  document.getElementById('menu-popup').classList.add('hidden');
}