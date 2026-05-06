document.addEventListener('DOMContentLoaded', () => {
  function selectedLabel(select) {
    const option = select.selectedOptions?.[0];

    return option && option.value !== '' ? option.textContent.trim() : '';
  }

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

  const profileForm = document.querySelector('.js-profile-form');
  const profileAvatarInput = profileForm?.querySelector('.js-profile-avatar-input');
  const profileEditToggle = profileForm?.querySelector('.js-profile-edit-toggle');
  const profileSaveButton = profileForm?.querySelector('.js-profile-save-button');
  const profileEditableFields = profileForm ? Array.from(profileForm.querySelectorAll('.js-profile-editable')) : [];
  const profileCountrySelect = profileForm?.querySelector('[data-profile-country]');
  const profileCitySelect = profileForm?.querySelector('[data-profile-city]');

  const showProfileSaveButton = () => {
    if (profileSaveButton) {
      profileSaveButton.hidden = false;
    }
  };

  const setProfileEditMode = (enabled) => {
    if (!profileForm) {
      return;
    }

    profileForm.classList.toggle('is-readonly', !enabled);
    profileForm.classList.toggle('is-editing', enabled);
    profileEditableFields.forEach((field) => {
      if (field.tagName === 'SELECT') {
        field.disabled = !enabled;
        const searchableInput = field.nextElementSibling?.querySelector?.('.searchable-select-input');

        if (searchableInput) {
          searchableInput.disabled = !enabled;
          searchableInput.value = selectedLabel(field);
        }

        return;
      }

      field.readOnly = !enabled;
    });

    if (enabled) {
      showProfileSaveButton();
      profileEditableFields[0]?.focus();
    }
  };

  profileEditToggle?.addEventListener('click', () => {
    setProfileEditMode(true);
  });

  profileEditableFields.forEach((field) => {
    field.addEventListener('input', showProfileSaveButton);
    field.addEventListener('change', showProfileSaveButton);
  });

  const syncProfileCities = () => {
    if (!profileCountrySelect || !profileCitySelect) {
      return;
    }

    const countryId = profileCountrySelect.value || '';

    Array.from(profileCitySelect.options).forEach((option) => {
      if (option.value === '') {
        option.hidden = false;
        return;
      }

      option.hidden = countryId !== '' && option.dataset.countryId !== countryId;
    });

    if (profileCitySelect.selectedOptions[0]?.hidden) {
      profileCitySelect.value = '';
    }
  };

  profileCountrySelect?.addEventListener('change', () => {
    if (profileCitySelect) {
      profileCitySelect.value = '';
    }

    syncProfileCities();
  });

  profileAvatarInput?.addEventListener('change', () => {
    if (!profileAvatarInput.files || profileAvatarInput.files.length === 0) {
      return;
    }

    const file = profileAvatarInput.files[0];
    const previewContainer = profileForm?.querySelector('.profile-avatar-preview');

    if (previewContainer && file.type.startsWith('image/')) {
      const previewUrl = URL.createObjectURL(file);
      let previewImage = previewContainer.querySelector('img');

      if (!previewImage) {
        previewContainer.textContent = '';
        previewImage = document.createElement('img');
        previewImage.className = 'js-profile-avatar-preview';
        previewImage.alt = 'Selected avatar preview';
        previewContainer.appendChild(previewImage);
      }

      previewImage.onload = () => URL.revokeObjectURL(previewUrl);
      previewImage.src = previewUrl;
    }

    showProfileSaveButton();
    setProfileEditMode(true);
  });

  if (profileForm) {
    setProfileEditMode(false);
    syncProfileCities();
  }

  const employerSearchForm = document.querySelector('.employer-search-shell');
  const employerCountrySelect = employerSearchForm?.querySelector('[data-employer-country]');
  const employerCitySelect = employerSearchForm?.querySelector('[data-employer-city]');
  const employerProficiencyRange = employerSearchForm?.querySelector('[data-employer-proficiency-range]');
  const employerProficiencyOutput = employerSearchForm?.querySelector('[data-employer-proficiency-output]');

  const syncEmployerCities = () => {
    if (!employerCountrySelect || !employerCitySelect) {
      return;
    }

    const countryId = employerCountrySelect.value || '';

    Array.from(employerCitySelect.options).forEach((option) => {
      if (option.value === '') {
        option.hidden = false;
        return;
      }

      option.hidden = countryId !== '' && option.dataset.countryId !== countryId;
    });

    if (employerCitySelect.selectedOptions[0]?.hidden) {
      employerCitySelect.value = '';
    }
  };

  employerCountrySelect?.addEventListener('change', () => {
    if (employerCitySelect) {
      employerCitySelect.value = '';
    }

    syncEmployerCities();
  });

  syncEmployerCities();

  employerProficiencyRange?.addEventListener('input', () => {
    if (employerProficiencyOutput) {
      employerProficiencyOutput.value = employerProficiencyRange.value;
      employerProficiencyOutput.textContent = employerProficiencyRange.value;
    }
  });

  const referenceTypeForSelect = (select) => {
    if (select.dataset.refType) {
      return select.dataset.refType;
    }

    const name = select.name || '';
    const fieldName = name.match(/\[([a-z_]+)\]$/)?.[1] || name;
    const action = select.form?.getAttribute('action') || '';

    if (fieldName === 'category_id') {
      return action.includes('find-cvs') ? 'cv_categories' : 'job_categories';
    }

    const map = {
      certificate_name_id: 'certificate_names',
      city_id: 'cities',
      country_id: 'countries',
      cv_category_id: 'cv_categories',
      degree_level_id: 'degree_levels',
      district_id: 'districts',
      employment_type_id: 'employment_types',
      gender_id: 'genders',
      industry_id: 'industries',
      institution_id: 'institutions',
      issuing_organization_id: 'issuing_organizations',
      job_category_id: 'job_categories',
      job_level_id: 'job_levels',
      job_title_id: 'job_titles',
      major_id: 'majors',
      minimum_degree_level_id: 'degree_levels',
      minimum_proficiency_level_id: 'skill_proficiency_levels',
      proficiency_level_id: 'skill_proficiency_levels',
      salary_range_id: 'salary_ranges',
      salary_type_id: 'salary_types',
      skill_id: 'skills',
      work_arrangement_id: 'work_arrangements',
    };

    return map[fieldName] || '';
  };

  const parentForReference = (select, type) => {
    if (type === 'cities') {
      return select.form?.querySelector('select[name="country_id"]')?.value || '';
    }

    if (type === 'districts') {
      return select.form?.querySelector('select[name="city_id"]')?.value || '';
    }

    return '';
  };

  const appUrl = (path) => `${window.OneCV?.basePath || ''}${path}`;

  const enhanceSearchableSelect = (select) => {
    const refType = referenceTypeForSelect(select);

    if (!refType || select.dataset.searchableReady === 'true') {
      return;
    }

    select.dataset.searchableReady = 'true';
    select.classList.add('searchable-select-source');

    const wrapper = document.createElement('div');
    wrapper.className = 'searchable-select';
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'searchable-select-input';
    input.placeholder = select.options[0]?.textContent.trim() || 'Search...';
    input.autocomplete = 'off';
    input.value = selectedLabel(select);
    input.disabled = select.disabled;

    const list = document.createElement('div');
    list.className = 'searchable-select-list';
    list.hidden = true;

    wrapper.append(input, list);
    select.insertAdjacentElement('afterend', wrapper);

    let activeItems = [];
    let abortController = null;

    const closeList = () => {
      list.hidden = true;
      list.innerHTML = '';
      activeItems = [];
    };

    const syncFromSelect = () => {
      input.value = selectedLabel(select);
      input.disabled = select.disabled;
    };

    const chooseItem = (item) => {
      let option = Array.from(select.options).find((current) => current.value === item.id);

      if (!option) {
        option = new Option(item.label, item.id);
        select.add(option);
      }

      select.value = item.id;
      input.value = item.label;
      select.dispatchEvent(new Event('change', { bubbles: true }));
      closeList();
    };

    const renderItems = (items) => {
      list.innerHTML = '';
      activeItems = items;

      if (items.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'searchable-select-empty';
        empty.textContent = 'No matching options';
        list.appendChild(empty);
        list.hidden = false;
        return;
      }

      items.forEach((item) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'searchable-select-option';
        button.textContent = item.label;
        button.addEventListener('mousedown', (event) => {
          event.preventDefault();
          chooseItem(item);
        });
        list.appendChild(button);
      });

      list.hidden = false;
    };

    const fetchItems = async () => {
      abortController?.abort();
      abortController = new AbortController();

      const params = new URLSearchParams({
        type: refType,
        q: input.value.trim(),
      });
      const parentId = parentForReference(select, refType);

      if (parentId !== '') {
        params.set('parent_id', parentId);
      }

      try {
        const response = await fetch(`${appUrl('/api/references')}?${params.toString()}`, {
          headers: { Accept: 'application/json' },
          signal: abortController.signal,
        });
        const data = response.ok ? await response.json() : { items: [] };

        renderItems(data.items || []);
      } catch (error) {
        if (error.name !== 'AbortError') {
          renderItems([]);
        }
      }
    };

    input.addEventListener('focus', fetchItems);
    input.addEventListener('input', fetchItems);
    input.addEventListener('blur', () => {
      window.setTimeout(() => {
        if (!activeItems.some((item) => item.label === input.value && item.id === select.value)) {
          syncFromSelect();
        }

        closeList();
      }, 140);
    });
    input.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        syncFromSelect();
        closeList();
      }
    });
    select.addEventListener('change', syncFromSelect);

    if (refType === 'cities' || refType === 'districts') {
      const parentName = refType === 'cities' ? 'country_id' : 'city_id';
      select.form?.querySelector(`select[name="${parentName}"]`)?.addEventListener('change', () => {
        input.value = '';
        closeList();
      });
    }
  };

  const initSearchableSelects = (root = document) => {
    root.querySelectorAll('select').forEach(enhanceSearchableSelect);
  };

  initSearchableSelects();

  const debounce = (callback, wait = 300) => {
    let timeoutId;

    return (...args) => {
      window.clearTimeout(timeoutId);
      timeoutId = window.setTimeout(() => callback(...args), wait);
    };
  };

  document.querySelectorAll('[data-ajax-search-form]').forEach((form) => {
    const results = form.querySelector('[data-ajax-search-results]');

    if (!results) {
      return;
    }

    let abortController;

    const syncRangeOutputs = () => {
      form.querySelectorAll('[data-employer-proficiency-range]').forEach((range) => {
        const output = form.querySelector('[data-employer-proficiency-output]');

        if (output) {
          output.textContent = range.value;
        }
      });
    };

    const urlFromForm = (page = '1') => {
      const url = new URL(form.action, window.location.origin);
      const params = new URLSearchParams(new FormData(form));

      Array.from(params.keys()).forEach((key) => {
        const values = params.getAll(key).filter((value) => value !== '');

        params.delete(key);
        values.forEach((value) => params.append(key, value));
      });

      if (page !== '1') {
        params.set('page', page);
      } else {
        params.delete('page');
      }

      url.search = params.toString();

      return url;
    };

    const fetchResults = async (url, pushState = true) => {
      abortController?.abort();
      abortController = new AbortController();

      const requestUrl = new URL(url.toString());
      requestUrl.searchParams.set('ajax', '1');
      results.classList.add('is-loading');

      try {
        const response = await fetch(requestUrl.toString(), {
          headers: {
            Accept: 'text/html+partial',
            'X-Requested-With': 'fetch',
          },
          signal: abortController.signal,
        });

        if (!response.ok) {
          throw new Error(`Search request failed with status ${response.status}`);
        }

        results.innerHTML = await response.text();
        initSearchableSelects(results);

        if (pushState) {
          window.history.pushState({}, '', url.toString());
        }
      } catch (error) {
        if (error.name !== 'AbortError') {
          results.innerHTML = `
            <section class="employer-empty-state">
              <span>error</span>
              <h2>Search could not be loaded</h2>
              <p>Please try again or refresh the page.</p>
            </section>
          `;
        }
      } finally {
        results.classList.remove('is-loading');
      }
    };

    const runSearch = (page = '1') => {
      syncRangeOutputs();
      fetchResults(urlFromForm(page));
    };

    const debouncedSearch = debounce(() => runSearch(), 320);

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      runSearch();
    });

    form.addEventListener('input', (event) => {
      if (event.target.matches('input[type="search"], input[type="range"]')) {
        debouncedSearch();
      }
    });

    form.addEventListener('change', (event) => {
      if (event.target.matches('select, input[type="checkbox"], input[type="range"]')) {
        runSearch();
      }
    });

    form.addEventListener('click', (event) => {
      const pageLink = event.target.closest('[data-ajax-search-page]');
      const clearLink = event.target.closest('[data-ajax-search-clear]');

      if (pageLink) {
        event.preventDefault();
        const url = new URL(pageLink.href);
        fetchResults(url);
        return;
      }

      if (!clearLink) {
        return;
      }

      event.preventDefault();
      form.querySelectorAll('input[type="search"]').forEach((input) => {
        input.value = '';
      });
      form.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
        checkbox.checked = false;
      });
      form.querySelectorAll('select').forEach((select) => {
        select.value = '';
        select.dispatchEvent(new Event('change', { bubbles: false }));
      });
      form.querySelectorAll('input[type="range"]').forEach((range) => {
        range.value = range.min || '1';
      });
      syncRangeOutputs();
      fetchResults(new URL(clearLink.href));
    });

    window.addEventListener('popstate', () => {
      window.location.reload();
    });

    syncRangeOutputs();
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
          addButton.disabled = items.length >= maxItems;
          addButton.setAttribute(
            'aria-label',
            items.length >= maxItems
              ? `Maximum ${maxItems} items reached`
              : addButton.dataset.defaultLabel || addButton.textContent.trim()
          );
        }
      }
    };

    const resetItem = (item) => {
      item.querySelectorAll('.searchable-select').forEach((wrapper) => wrapper.remove());
      item.querySelectorAll('input, select, textarea').forEach((field) => {
        field.classList.remove('searchable-select-source');
        delete field.dataset.searchableReady;

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

    dynamicBuilderForm.querySelectorAll('.js-add-dynamic-row').forEach((button) => {
      button.dataset.defaultLabel = button.textContent.trim();
    });

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
        if (button.disabled) {
          return;
        }

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
        initSearchableSelects(item);
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
