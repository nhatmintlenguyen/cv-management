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

  document.querySelectorAll('.flash').forEach((flash) => {
    window.setTimeout(() => {
      flash.classList.add('is-hiding');
      window.setTimeout(() => flash.remove(), 220);
    }, 3200);
  });

  const dynamicBuilderForm = document.querySelector('.js-dynamic-builder-form');

  if (dynamicBuilderForm) {
    const prefixes = {
      education: 'educations',
      work: 'work_histories',
      certificate: 'certificates',
      skill: 'skills',
    };

    const updateList = (list, prefix) => {
      const items = Array.from(list.querySelectorAll('[data-dynamic-item]'));
      const maxItems = Number.parseInt(list.dataset.maxItems || '0', 10);

      items.forEach((item, index) => {
        const number = item.querySelector('[data-item-number]');

        if (number) {
          number.textContent = String(index + 1);
        }

        item.querySelectorAll('[name]').forEach((field) => {
          field.name = field.name.replace(new RegExp(`${prefix}\\[\\d+\\]`), `${prefix}[${index}]`);
        });

        const removeButton = item.querySelector('.js-remove-dynamic-row');
        if (removeButton) {
          removeButton.disabled = items.length === 1;
        }
      });

      if (maxItems > 0) {
        const addButton = dynamicBuilderForm.querySelector(`.js-add-dynamic-row[data-target="${list.dataset.dynamicList}"]`);

        if (addButton) {
          addButton.hidden = items.length >= maxItems;
        }
      }
    };

    const resetItem = (item) => {
      item.querySelectorAll('input, select, textarea').forEach((field) => {
        if (field.type === 'checkbox') {
          field.checked = false;
          return;
        }

        field.value = '';
        field.disabled = false;
      });
    };

    const syncCurrentRole = (item) => {
      const currentCheckbox = item.querySelector('input[name*="[is_current]"]');
      const endYearInput = item.querySelector('input[name*="[end_year]"]');

      if (!currentCheckbox || !endYearInput) {
        return;
      }

      endYearInput.disabled = currentCheckbox.checked;
      if (currentCheckbox.checked) {
        endYearInput.value = '';
      }
    };

    dynamicBuilderForm.querySelectorAll('[data-dynamic-list]').forEach((list) => {
      const type = list.dataset.dynamicList;
      const prefix = prefixes[type];

      if (!prefix) {
        return;
      }

      updateList(list, prefix);
      list.querySelectorAll('[data-dynamic-item]').forEach(syncCurrentRole);
    });

    dynamicBuilderForm.querySelectorAll('.js-add-dynamic-row').forEach((button) => {
      button.addEventListener('click', () => {
        const type = button.dataset.target;
        const list = dynamicBuilderForm.querySelector(`[data-dynamic-list="${type}"]`);
        const prefix = prefixes[type];
        const firstItem = list?.querySelector('[data-dynamic-item]');

        if (!list || !prefix || !firstItem) {
          return;
        }

        const item = firstItem.cloneNode(true);
        resetItem(item);
        list.appendChild(item);
        updateList(list, prefix);
      });
    });

    dynamicBuilderForm.addEventListener('click', (event) => {
      const removeButton = event.target.closest('.js-remove-dynamic-row');

      if (!removeButton) {
        return;
      }

      const item = removeButton.closest('[data-dynamic-item]');
      const list = item?.closest('[data-dynamic-list]');
      const prefix = list ? prefixes[list.dataset.dynamicList] : null;

      if (!item || !list || !prefix || list.querySelectorAll('[data-dynamic-item]').length <= 1) {
        return;
      }

      item.remove();
      updateList(list, prefix);
    });

    dynamicBuilderForm.addEventListener('change', (event) => {
      if (!event.target.matches('input[name*="[is_current]"]')) {
        return;
      }

      const item = event.target.closest('[data-dynamic-item]');
      if (item) {
        syncCurrentRole(item);
      }
    });
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
