document.addEventListener('DOMContentLoaded', function() {
  const toggle = document.querySelector('.mobile-menu-toggle');
  const sidebar = document.getElementById('mobile-sidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  
  if (toggle) {
    toggle.addEventListener('click', function() {
      document.body.classList.toggle('sidebar-active');
      // Update aria-hidden state
      const isHidden = sidebar.getAttribute('aria-hidden') === 'true';
      sidebar.setAttribute('aria-hidden', !isHidden);
      toggle.setAttribute('aria-expanded', !isHidden);
    });
    
    overlay.addEventListener('click', function() {
      document.body.classList.remove('sidebar-active');
      sidebar.setAttribute('aria-hidden', 'true');
      toggle.setAttribute('aria-expanded', 'false');
    });
  }
});