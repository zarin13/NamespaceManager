<?php
/**
 * NamespaceManager
 * Hooks
 * @author Jeffrey Wang for MyWikis LLC
 */

class NamespaceManagerHooks {
    public static function onCanonicalNamespaces(array &$namespaces) {
        $data = NamespaceManagerHooks::loadNamespaceData();

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
            str_replace(' ', '_', $namespaceName);
            $namespaces[$count++] = $namespaceName;
            $namespaces[$count++] = $namespaceName . '_talk';
        }
    }

    /**
     * Retrieves JSON files
     * @return associated_array if success
     *         false if failure
    */
    private static function loadNamespaceData() {
        global $wgDBname, $wgNamespaceManagerDir;

        $wgNamespaceManagerDir = str_replace('$1', $wgDBname, $wgNamespaceManagerDir);

        // Absolute vs. relative path
        $filepath = substr($wgNamespaceManagerDir, 0, 1) === '/'
            ? $wgNamespaceManagerDir
            : __DIR__ . '/../'. $wgNamespaceManagerDir;
        
        $fileContents = file_get_contents($filepath);
        if ($fileContents === false) {
            return false;
        }
        $data = json_decode($fileContents, true);
        return $data ?? false;
    }
}