document.addEventListener('DOMContentLoaded', () => {
  const identityForm = document.querySelector('.js-builder-identity-form');

  if (identityForm) {
    const countrySelect = identityForm.querySelector('[data-location-country]');
    const citySelect = identityForm.querySelector('[data-location-city]');
    const districtSelect = identityForm.querySelector('[data-location-district]');

    const filterOptions = (select, dataKey, parentValue) => {
      if (!select) {
        return;
      }

      Array.from(select.options).forEach((option) => {
        if (option.value === '') {
          option.hidden = false;
          return;
        }

        option.hidden = parentValue === '' || option.dataset[dataKey] !== parentValue;
      });

      if (select.selectedOptions[0]?.hidden) {
        select.value = '';
      }
    };

    const syncLocationFields = () => {
      const countryId = countrySelect?.value || '';
      const cityId = citySelect?.value || '';

      filterOptions(citySelect, 'countryId', countryId);
      filterOptions(districtSelect, 'cityId', cityId);
    };

    countrySelect?.addEventListener('change', () => {
      if (citySelect) {
        citySelect.value = '';
      }

      if (districtSelect) {
        districtSelect.value = '';
      }

      syncLocationFields();
    });

    citySelect?.addEventListener('change', () => {
      if (districtSelect) {
        districtSelect.value = '';
      }

      syncLocationFields();
    });

    syncLocationFields();
  }

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
