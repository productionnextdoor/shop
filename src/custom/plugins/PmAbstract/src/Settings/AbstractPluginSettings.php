<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 08.04.22
 * Time: 13:20
 */

namespace PmAbstract\Settings;

use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * Service to access the plugins configurations
 *
 * USAGE:
 * Add config variables as simple member variables
 * They are filled automatically in the constructor
 * if they have the same name as the configuration item
 *
 * EXAMPLE concrete service in services.xml:
 *    Replace PLUGIN_NAME with your plugin's name
 *
 * <service id="pm.PLUGIN_NAME.plugin_settings"
 *          class="%pm.PLUGIN_NAME.plugin_settings_class%"
 *          parent="pm.abstract.plugin_settings_class_abstract"
 * >
 *     <argument type="string">PLUGIN_NAME</argument>
 * </service>
 */
abstract class AbstractPluginSettings
{

    /**
     * AbstractPluginSettings constructor.
     * @param SystemConfigService $systemConfigService
     * @param string $pluginName
     */
    public function __construct(
        protected readonly SystemConfigService $systemConfigService,
        protected readonly string $pluginName
    ) {
        $this->initMemberVariables();
    }

    /**
     * Automatically fills the member variables with the corresponding
     * config value.
     * The member variables must be named like the config element's name
     */
    protected function initMemberVariables(): void
    {
        $fields = get_object_vars($this);
        $fieldNames = array_diff(
            array_keys($fields),
            ['systemConfigService', 'pluginName', 'salesChannelId']
        );
        foreach ($fieldNames as $fieldName) {
            $this->{$fieldName} = $this->getConfig($fieldName);
        }
    }

    /**
     * Returns a config element by the name
     * Default is returned if the name does not exist
     *
     * @param string $key
     * @param mixed|null $default
     * @param string|null $salesChannelId
     * @return mixed|null
     */
    public function getConfig(string $key, $default = null, ?string $salesChannelId = null)
    {
        return $this->systemConfigService->get(
            "{$this->pluginName}.config.{$key}",
            $salesChannelId
        ) ?? $default;
    }
}
