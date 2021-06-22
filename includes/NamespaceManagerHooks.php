<?php
/**
 * NamespaceManager
 * Hooks
 * @author Jeffrey Wang for MyWikis LLC
 */

class NamespaceManagerHooks {
    /**
     * Sets up namespace configuration options
     * In MediaWiki, the configuration must be done _before_ the namespaces themselves are actually
     * initialized, believe it or not.
     * i.e. This is called before the CanonicalNamespaces hook is called.
     */
    public static function onMediaWikiServices(MediaWikiServices &$services) {
        global $wgCapitalLinkOverrides,
            $wgCapitalLinks,
            $wgContentNamespaces,
            // $wgExtraSignatureNamespaces,
            // $wgNamespaceContentModels,
            $wgNamespaceAliases,
            $wgNamespaceProtection,
            $wgNamespacesWithSubpages,
            $wgNamespacesToBeSearchedDefault,
            $wgNonincludableNamespaces;
        
        $data = NamespaceManager::loadNamespaceData();
        
        if ($data === false) {
            return;
        }

        $count = 3000;
        // Using JSON configuration, set MediaWiki config variables
        foreach ($data as $namespaceDefinition) {
            // Is content namespace
            if ($namespaceDefinition['content'] === true) {
                $wgContentNamespaces[] = $count;
            }

            // Is in search results by default
            $wgNamespacesToBeSearchedDefault[$count] = $namespaceDefinition['searchdefault'] ?? false;
            $wgNamespacesToBeSearchedDefault[$count + 1] = $namespaceDefinition['talksearchdefault'] ?? false;

            // Supports subpages
            $wgNamespacesWithSubpages[$count] = $namespaceDefinition['subpages'] ?? false;
            $wgNamespacesWithSubpages[$count + 1] = $namespaceDefinition['talksubpages'] ?? false;

            // Is includable
            if ($namespaceDefinition['includable'] === false) {
                $wgNonincludableNamespaces[] = $count;
            }
            if ($namespaceDefinition['talkincludable'] === false) {
                $wgNonincludableNamespaces[] = $count + 1;
            }

            // Aliases for non-talk
            if ($namespaceDefinition['aliases'] !== null
                    && !empty($namespaceDefinition['aliases'])) {
                foreach ($namespaceDefinition['aliases'] as $alias) {
                    $alias = NamespaceManager::prepareNamespaceName($alias);
                    $wgNamespaceAliases[$alias] = $count;
                }
            }
            // Aliases for talk
            if ($namespaceDefinition['talkaliases'] !== null
                    && !empty($namespaceDefinition['talkaliases'])) {
                foreach ($namespaceDefinition['talkaliases'] as $alias) {
                    $alias = NamespaceManager::prepareNamespaceName($alias);
                    $wgNamespaceAliases[$alias] = $count + 1;
                }
            }

            // Edit permissions for non-talk
            if ($namespaceDefinition['editpermissions'] !== null
                    && !empty($namespaceDefinition['editpermissions'])) {
                $wgNamespaceProtection[$count] = $namespaceDefinition['editpermissions'] ?? [];
            }
            // Edit permissions for talk
            if ($namespaceDefinition['talkeditpermissions'] !== null
                    && !empty($namespaceDefinition['talkeditpermissions'])) {
                $wgNamespaceProtection[$count + 1] = $namespaceDefinition['talkeditpermissions']
                        ?? [];
            }

            $count++;
        }
    }

    /**
     * Adds the namespaces themselves
     */
    public static function onCanonicalNamespaces(array &$namespaces) {
        $data = NamespaceManager::loadNamespaceData();

        // If namespace defn file doesn't exist, skip silently rather than crashing MediaWiki
        if ($data === false) {
            return;
        }

        $count = 3000;
        foreach ($data as $namespaceDefinition) {
            $namespaceName = $namespaceDefinition['name'];
            if ($namespaceName === null) {
                wfDebugLog('NamespaceManager', 'Invalid namespace definition.');
                return;
            }
            $namespaceName = NamespaceManager::prepareNamespaceName($namespaceName);
            $namespaces[$count++] = $namespaceName;
            $namespaces[$count++] = $namespaceName . '_talk';
        }
    }
}