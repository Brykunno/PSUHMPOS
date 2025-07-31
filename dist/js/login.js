document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
  
    const formData = new FormData(this);
  
    try {
      const response = await fetch('login-v2.php', {
        method: 'POST',
        body: formData
      });
  
      const data = await response.json();
  
      if (data.status === 'success') {
        // Redirect based on user type
        switch (data.type) {
          case 1:
            window.location.href = 'admin/home.php';
            break;
          case 2:
            window.location.href = 'admin/sales/manage_sale.php';
            break;
          case 3:
            window.location.href = 'admin/kitchen/index.php';
            break;
          default:
            document.getElementById('login-msg').innerText = 'Unknown user type.';
        }
      } else {
        document.getElementById('login-msg').innerText = data.message;
      }
    } catch (error) {
      console.error('Login error:', error);
      document.getElementById('login-msg').innerText = 'Server error.';
    }
  });
  