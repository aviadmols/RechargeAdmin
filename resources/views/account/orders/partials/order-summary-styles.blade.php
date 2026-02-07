<style>
  :root {
    --order-primary: #002642;
    --order-text: #000;
    --order-white: #FFF;
    --order-bg-light: #F9FAFB;
    --order-font: "DM Sans", sans-serif;
    --order-spacing-xs: 8px;
    --order-spacing-sm: 16px;
    --order-spacing-md: 19px;
    --order-spacing-lg: 22px;
    --order-spacing-xl: 28px;
    --order-radius-sm: 8px;
    --order-radius-md: 12px;
  }

  .order-summary {
    max-width: 100%;
    margin: 0 auto;
    background: #D7ECFF;
    padding: var(--order-spacing-xl) var(--order-spacing-md);
    font-family: var(--order-font);
    position: relative;
    border-radius: var(--order-radius-md);
  }

  .order-summary-back {
    display: inline-flex;
    align-items: center;
    gap: var(--order-spacing-xs);
    color: var(--order-primary);
    font-family: var(--order-font);
    font-size: 14px;
    text-decoration: none;
    margin-bottom: var(--order-spacing-sm);
  }
  .order-summary-back:hover { opacity: 0.85; }

  .order-summary-header {
    text-align: center;
    margin-bottom: var(--order-spacing-sm);
  }

  .order-summary-title {
    font-family: var(--order-font);
    font-size: 22px;
    font-weight: 700;
    color: var(--order-primary);
    margin: 0 0 4px 0;
    letter-spacing: -0.06rem;
  }

  .order-summary-subtitle {
    font-size: 16px;
    font-weight: 500;
    color: #647f94;
    margin: 0;
  }

  .order-items-list {
    list-style: none;
    margin: 0 0 var(--order-spacing-lg) 0;
    padding: 0;
    max-width: 1000px;
    margin-left: auto;
    margin-right: auto;
  }

  .order-item {
    display: flex;
    align-items: flex-start;
    border-bottom: 1px solid rgba(100, 127, 148, 0.33);
    padding-bottom: 10px;
    gap: 12px;
    margin-bottom: 8px;
    list-style: none;
    text-decoration: none;
    color: inherit;
  }
  .order-item:first-child {
    border-top: 1px solid rgba(100, 127, 148, 0.33);
    padding-top: 8px;
  }
  .order-item:hover { opacity: 0.95; }

  .order-item-checkbox {
    margin-top: 3px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
  }
  .order-item-checkbox svg circle { fill: #3baf79; }

  .order-item-content {
    flex-grow: 1;
    min-width: 0;
  }

  .order-item-title {
    font-size: 14px;
    color: var(--order-primary);
    font-family: var(--order-font);
    font-weight: 600;
  }

  .order-item-meta {
    font-size: 13px;
    color: #647f94;
    margin-top: 2px;
  }

  .order-item-right {
    flex-shrink: 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--order-primary);
  }

  .order-summary-price-section {
    max-width: 1000px;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 0;
  }

  .order-summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    margin-bottom: 0;
    background: var(--order-white);
    border-radius: 0 0 var(--order-radius-sm) var(--order-radius-sm);
    border-top: 1px solid rgba(100, 127, 148, 0.2);
  }

  .order-summary-total-label {
    color: var(--order-primary);
    font-size: 14px;
    font-weight: 400;
    font-family: var(--order-font);
  }

  .order-summary-total-current {
    color: var(--order-primary);
    font-size: 14px;
    font-weight: 700;
    font-family: var(--order-font);
  }

  .order-summary-shipping {
    max-width: 1000px;
    margin: var(--order-spacing-sm) auto 0;
    padding: var(--order-spacing-sm) 15px;
    background: rgba(255,255,255,0.6);
    border-radius: var(--order-radius-sm);
    font-family: var(--order-font);
    font-size: 14px;
    color: var(--order-primary);
  }
  .order-summary-shipping h3 {
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 6px 0;
    color: var(--order-primary);
  }
  .order-summary-shipping p { margin: 0; line-height: 1.5; }

  .order-empty {
    text-align: center;
    list-style: none;
    min-height: 20vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--order-spacing-xl);
    color: var(--order-text);
    font-size: 18px;
    font-family: var(--order-font);
  }

  .order-nav {
    max-width: 1000px;
    margin: var(--order-spacing-sm) auto 0;
    display: flex;
    justify-content: center;
    gap: 16px;
    font-family: var(--order-font);
  }
  .order-nav a {
    color: var(--order-primary);
    font-weight: 600;
    text-decoration: none;
  }
  .order-nav a:hover { text-decoration: underline; }

  .order-item-image {
    width: 48px;
    height: 48px;
    border-radius: var(--order-radius-sm);
    object-fit: cover;
    flex-shrink: 0;
    background: #E0E0E0;
  }

  .order-filters {
    max-width: 1000px;
    margin: 0 auto var(--order-spacing-sm);
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
    font-family: var(--order-font);
  }
  .order-filters input {
    border: 1px solid #657f9454;
    border-radius: var(--order-radius-sm);
    padding: 8px 12px;
    font-size: 14px;
    color: var(--order-primary);
  }
  .order-filters input:focus {
    outline: none;
    border-color: var(--order-primary);
    box-shadow: 0 0 0 2px rgba(0,38,66,0.15);
  }
  .order-filters button {
    background: var(--order-primary);
    color: var(--order-white);
    border: none;
    border-radius: 30px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    font-family: var(--order-font);
  }
  .order-filters button:hover { opacity: 0.9; }

  .order-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 6px;
  }
  .order-badge-success { background: #AEE8C3; color: var(--order-primary); }
  .order-badge-default { background: #e0e0e0; color: #647f94; }
  .order-badge-error { background: #ffcdd2; color: #b71c1c; }
</style>
