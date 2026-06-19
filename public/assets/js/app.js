(function () {
  const state = {
    catalog: null,
    settings: null,
    paypal: null,
    lang: localStorage.getItem('tp_lang') || 'en',
    selectedPlan: null,
    selectedGuide: 0,
    paypalScriptLoaded: false,
  };

  const i18n = window.TruePointI18n || { en: {}, localized: {}, countryNames: {} };
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));

  function t(key) {
    return (i18n.localized[state.lang] && i18n.localized[state.lang][key])
      || i18n.en[key]
      || key;
  }

  function countryName(country) {
    const localized = i18n.countryNames[state.lang] || {};
    return localized[country.code] || country.name;
  }

  function formatPrice(value) {
    return new Intl.NumberFormat(state.lang === 'en' ? 'en' : state.lang, {
      style: 'currency',
      currency: state.paypal?.currency || 'EUR',
      maximumFractionDigits: 0,
    }).format(value);
  }

  async function init() {
    try {
      const response = await fetch('/api/catalog.php', { headers: { Accept: 'application/json' } });
      const data = await response.json();
      if (!data.ok) throw new Error(data.error || 'Catalog failed');
      state.catalog = data.content;
      state.settings = data.settings;
      state.paypal = data.paypal;
    } catch (error) {
      console.error(error);
      state.catalog = fallbackCatalog();
      state.settings = {};
      state.paypal = { configured: false, currency: 'EUR', clientId: '' };
    }

    setupLanguageSelect();
    applyTranslations();
    renderCountries();
    renderCapabilities();
    renderPricing();
    renderGuides();
    renderFaqs();
    bindCheckout();
  }

  function setupLanguageSelect() {
    const select = $('#languageSelect');
    select.innerHTML = '';
    const languages = state.catalog.languages || [{ code: 'en', native: 'English' }];
    for (const language of languages) {
      const option = document.createElement('option');
      option.value = language.code;
      option.textContent = language.native || language.label || language.code.toUpperCase();
      select.append(option);
    }
    if (!languages.some((language) => language.code === state.lang)) {
      state.lang = 'en';
    }
    select.value = state.lang;
    select.addEventListener('change', () => {
      state.lang = select.value;
      localStorage.setItem('tp_lang', state.lang);
      document.documentElement.lang = state.lang;
      applyTranslations();
      renderCountries();
      renderCapabilities();
      renderPricing();
      renderGuides();
      renderFaqs();
      updateCheckoutLabels();
    });
  }

  function applyTranslations() {
    document.documentElement.lang = state.lang;
    $$('[data-i18n]').forEach((node) => {
      const value = t(node.dataset.i18n);
      if (Array.isArray(value)) return;
      node.textContent = value;
    });
    updateCheckoutLabels();
  }

  function renderCountries() {
    const exclusive = $('#exclusiveCountries');
    const online = $('#onlineCountries');
    exclusive.innerHTML = '';
    online.innerHTML = '';
    for (const country of state.catalog.countries || []) {
      const item = document.createElement('li');
      item.textContent = countryName(country);
      (country.rights === 'online' ? online : exclusive).append(item);
    }
  }

  function renderCapabilities() {
    const grid = $('#capabilityGrid');
    grid.innerHTML = '';
    for (const item of state.catalog.capabilities || []) {
      const card = document.createElement('article');
      card.className = 'capability-card';
      card.innerHTML = `
        <span class="material-symbols-outlined" aria-hidden="true">${item.icon}</span>
        <h3>${escapeHtml(t(item.title_key))}</h3>
        <p>${escapeHtml(t(item.body_key))}</p>
      `;
      grid.append(card);
    }
  }

  function renderPricing() {
    const grid = $('#pricingGrid');
    grid.innerHTML = '';
    for (const plan of state.catalog.plans || []) {
      const card = document.createElement('article');
      card.className = `pricing-card${plan.featured ? ' featured' : ''}`;
      const buttonTag = plan.featured ? 'md-filled-button' : 'md-outlined-button';
      card.innerHTML = `
        ${plan.featured ? `<span class="best-value">${escapeHtml(t('pricing.best'))}</span>` : ''}
        <h3>${escapeHtml(t(plan.name_key))}</h3>
        <p>${escapeHtml(t(plan.persona_key))}</p>
        <div class="price">${formatPrice(plan.price)} <small>${escapeHtml(t('pricing.beforeTax'))}</small></div>
        <ul>
          <li>${escapeHtml(t('pricing.feature.1'))}</li>
          <li>${escapeHtml(t('pricing.feature.2'))}</li>
          <li>${escapeHtml(t('pricing.feature.3'))}</li>
          <li>${escapeHtml(t('pricing.feature.4'))}</li>
        </ul>
        <${buttonTag} data-plan-trigger="${plan.id}">${escapeHtml(t('pricing.choose'))}</${buttonTag}>
      `;
      grid.append(card);
    }
    $$('#pricingGrid [data-plan-trigger]').forEach((button) => {
      button.addEventListener('click', () => openCheckout(button.dataset.planTrigger));
    });
  }

  function renderGuides() {
    const tabs = $('#guideTabs');
    const panel = $('#guidePanel');
    const guides = state.catalog.receiver_guides || [];
    tabs.innerHTML = '';
    guides.forEach((guide, index) => {
      const button = document.createElement('button');
      button.className = 'guide-tab';
      button.type = 'button';
      button.role = 'tab';
      button.textContent = guide.brand;
      button.setAttribute('aria-selected', index === state.selectedGuide ? 'true' : 'false');
      button.addEventListener('click', () => {
        state.selectedGuide = index;
        renderGuides();
      });
      tabs.append(button);
    });

    const guide = guides[state.selectedGuide] || guides[0];
    if (!guide) return;
    const steps = t(guide.steps_key);
    panel.innerHTML = `
      <h3>${escapeHtml(guide.brand)}</h3>
      <ol>${(Array.isArray(steps) ? steps : []).map((step) => `<li>${escapeHtml(step)}</li>`).join('')}</ol>
    `;
  }

  function renderFaqs() {
    const list = $('#faqList');
    list.innerHTML = '';
    for (const faq of state.catalog.faqs || []) {
      const details = document.createElement('details');
      details.className = 'faq-item';
      details.innerHTML = `<summary>${escapeHtml(t(faq.q_key))}</summary><p>${escapeHtml(t(faq.a_key))}</p>`;
      list.append(details);
    }
  }

  function bindCheckout() {
    $('#closeCheckout').addEventListener('click', () => $('#checkoutDialog').close());
    $$('[data-plan-trigger]').forEach((button) => {
      button.addEventListener('click', () => openCheckout(button.dataset.planTrigger));
    });
  }

  function updateCheckoutLabels() {
    const labels = [
      ['#customerName', 'checkout.name'],
      ['#customerEmail', 'checkout.email'],
      ['#customerReceiver', 'checkout.receiver'],
      ['#customerNotes', 'checkout.notes'],
    ];
    for (const [selector, key] of labels) {
      const field = $(selector);
      if (field) field.setAttribute('label', t(key));
    }
  }

  function openCheckout(planId) {
    const plan = (state.catalog.plans || []).find((item) => item.id === planId) || state.catalog.plans?.[0];
    if (!plan) return;
    state.selectedPlan = plan;

    $('#selectedPlanSummary').textContent = `${t(plan.name_key)} - ${formatPrice(plan.price)} ${t('pricing.beforeTax')}`;
    renderCountrySelect();
    $('#checkoutStatus').textContent = '';
    $('#paypalButtons').innerHTML = '';

    const notice = $('#paypalSetupNotice');
    if (!state.paypal?.configured) {
      notice.textContent = t('checkout.configure');
      notice.classList.remove('hidden');
    } else {
      notice.classList.add('hidden');
      renderPayPalButtons();
    }

    $('#checkoutDialog').show();
  }

  function renderCountrySelect() {
    const select = $('#customerCountry');
    const prior = select.value;
    select.innerHTML = '';
    for (const country of state.catalog.countries || []) {
      const option = document.createElement('option');
      option.value = country.code;
      option.textContent = countryName(country);
      select.append(option);
    }
    if (prior) select.value = prior;
  }

  async function renderPayPalButtons() {
    await loadPayPalScript();
    if (!window.paypal) {
      $('#checkoutStatus').textContent = t('checkout.error');
      return;
    }

    window.paypal.Buttons({
      style: { layout: 'vertical', color: 'gold', shape: 'rect', label: 'paypal' },
      createOrder: async () => {
        const payload = checkoutPayload();
        if (!payload) throw new Error('Missing customer fields');
        $('#checkoutStatus').textContent = t('checkout.creating');
        const response = await fetch('/api/create-order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify(payload),
        });
        const data = await response.json();
        if (!data.ok) throw new Error(data.error || 'PayPal order failed');
        state.pendingLocalOrder = data.local_id;
        return data.paypal_order_id;
      },
      onApprove: async (data) => {
        $('#checkoutStatus').textContent = t('checkout.capturing');
        const response = await fetch('/api/capture-order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ paypal_order_id: data.orderID, local_id: state.pendingLocalOrder }),
        });
        const result = await response.json();
        if (!result.ok) throw new Error(result.error || 'Payment capture failed');
        showSuccess(result.order);
      },
      onError: (error) => {
        console.error(error);
        $('#checkoutStatus').textContent = t('checkout.error');
      },
    }).render('#paypalButtons');
  }

  function checkoutPayload() {
    const payload = {
      plan_id: state.selectedPlan.id,
      customer: {
        name: $('#customerName').value.trim(),
        email: $('#customerEmail').value.trim(),
        country: $('#customerCountry').value,
        receiver: $('#customerReceiver').value.trim(),
        notes: $('#customerNotes').value.trim(),
      },
    };

    if (!payload.customer.name || !payload.customer.email || !payload.customer.country) {
      $('#checkoutStatus').textContent = t('checkout.required');
      return null;
    }

    return payload;
  }

  function showSuccess(order) {
    const provisioning = order.provisioning || {};
    $('#checkoutStatus').innerHTML = `
      <strong>${escapeHtml(t('checkout.success'))}</strong><br>
      NTRIP: ${escapeHtml(provisioning.ntrip_host || '')}:${escapeHtml(String(provisioning.ntrip_port || ''))}<br>
      Mountpoint: ${escapeHtml(provisioning.mountpoint || '')}<br>
      Username: ${escapeHtml(provisioning.username || '')}
    `;
  }

  function loadPayPalScript() {
    if (state.paypalScriptLoaded) return Promise.resolve();
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      const params = new URLSearchParams({
        'client-id': state.paypal.clientId,
        currency: state.paypal.currency || 'EUR',
        intent: 'capture',
      });
      script.src = `https://www.paypal.com/sdk/js?${params.toString()}`;
      script.onload = () => {
        state.paypalScriptLoaded = true;
        resolve();
      };
      script.onerror = reject;
      document.head.append(script);
    });
  }

  function escapeHtml(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function fallbackCatalog() {
    return {
      languages: [{ code: 'en', native: 'English' }],
      plans: [
        { id: 'monthly', name_key: 'plan.monthly.name', persona_key: 'plan.monthly.persona', price: 50 },
        { id: 'quarterly', name_key: 'plan.quarterly.name', persona_key: 'plan.quarterly.persona', price: 120 },
        { id: 'annual', name_key: 'plan.annual.name', persona_key: 'plan.annual.persona', price: 380, featured: true },
      ],
      countries: [],
      capabilities: [],
      receiver_guides: [],
      faqs: [],
    };
  }

  document.addEventListener('DOMContentLoaded', init);
})();
