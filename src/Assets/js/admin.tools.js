/**
  * Tools Manager JS
  *
  * @package: ProjectPress
  * @subpackage: Admin\Manager\Tools
  * @version 1.0.0
  * @since 1.0.0
  */
(() => {
  /**
   * Initialize integration-tool controls.
   *
   * @since 1.0.0
   */
  const toolsIntegration = document.getElementById('projectpress-tools-integration-page');
  toolsIntegration && (() => {
    /**
     * Show or hide integration block based on switch state.
     * 
     * @since 1.0.0
     */
    const enableIntegrations = document.getElementById('projectpress-enable-integrations');
    const integrationBlock = document.getElementById('projectpress-integrations');

    enableIntegrations.addEventListener('change', function () {
      // Toggle d-none class based on switch state
      if (this.checked) {
        integrationBlock.classList.remove('d-none');
      } else {
        integrationBlock.classList.add('d-none');
      }
    });
  })();

  /**
   * Initialize reset-tool controls.
   *
   * @since 1.0.0
   */
  const initResetTools = () => {
    const toolsReset = document.querySelector('.projectpress-tools-reset-page');
    if (!toolsReset) {
      return;
    }

    const resetScope = toolsReset.querySelector('#projectpress-reset-scope');
    const resetPlugins = toolsReset.querySelector('#projectpress-reset-plugins');
    if (!resetScope || !resetPlugins) {
      return;
    }

    const applyResetScopeState = () => {
      const isPluginsScope = resetScope.value === 'plugins';
      resetPlugins.hidden = !isPluginsScope;
      resetPlugins.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
        checkbox.disabled = !isPluginsScope;
      });
    };

    if (resetScope.dataset.projectpressResetScopeBound !== 'true') {
      resetScope.dataset.projectpressResetScopeBound = 'true';
      resetScope.addEventListener('change', applyResetScopeState);
    }

    applyResetScopeState();
  };

  const initToolsPages = () => {
    initResetTools();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initToolsPages, { once: true });
  } else {
    initToolsPages();
  }
})();
