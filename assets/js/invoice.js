// Invoice page: WhatsApp share (customer/invoice.php)

function shareWhatsApp(orderId) {
  const btn = document.getElementById('waBtn');
  if (btn) { btn.disabled = true; btn.textContent = 'Preparing...'; }

  fetch('../api/whatsapp.php?id=' + orderId)
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        window.open(res.link, '_blank');
      } else {
        showToast(res.message || 'Could not build WhatsApp message');
      }
    })
    .catch(() => showToast('Network error'))
    .finally(() => {
      if (btn) { btn.disabled = false; btn.textContent = '🟢 Share on WhatsApp'; }
    });
}
