document.addEventListener('DOMContentLoaded', () => {
  const modal = document.querySelector('#reference-edit-modal');

  if (!modal) {
    return;
  }

  const idInput = modal.querySelector('#reference-modal-id');
  const openButtons = document.querySelectorAll('.js-open-reference-modal');
  const closeButtons = modal.querySelectorAll('.js-close-reference-modal');

  openButtons.forEach((button) => {
    button.addEventListener('click', () => {
      idInput.value = button.dataset.id || '';

      Object.entries(button.dataset).forEach(([key, value]) => {
        if (key === 'id') {
          return;
        }

        const fieldName = key.replace(/[A-Z]/g, (letter) => `_${letter.toLowerCase()}`);
        const field = modal.querySelector(`[name="${fieldName}"]`);

        if (field) {
          field.value = value;
        }
      });

      modal.hidden = false;
      document.body.classList.add('modal-open');
    });
  });

  closeButtons.forEach((button) => {
    button.addEventListener('click', () => {
      modal.hidden = true;
      document.body.classList.remove('modal-open');
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !modal.hidden) {
      modal.hidden = true;
      document.body.classList.remove('modal-open');
    }
  });
});
