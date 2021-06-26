<?php
/**
 * NamespaceManager
 * Main
 * @author Jeffrey Wang for MyWikis LLC
 */

class NamespaceManager {
    /**
     * Retrieves JSON files
     * @return associated_array if success
     *         false if failure
    */
    public static function loadNamespaceData() {
        global $wgDBname, $wgNamespaceManagerDataPath;

        $wgNamespaceManagerDataPath = str_replace('$1', $wgDBname, $wgNamespaceManagerDataPath);

        // Absolute vs. relative path
        $filepath = substr($wgNamespaceManagerDataPath, 0, 1) === '/'
            ? $wgNamespaceManagerDataPath
            : __DIR__ . '/../'. $wgNamespaceManagerDataPath;
        
        $fileContents = file_get_contents($filepath);
        if ($fileContents === false) {
            return false;
        }
        $data = json_decode($fileContents, true);
        return $data ?? false;
    }

    /**
     * Changes the pretty namespace name specification to the technical version
     * For instance, "A Namespace" becomes "A_Namespace" and "A Namespace talk" becomes
     * "A_Namespace_talk"
     */
    public static function prepareNamespaceName($namespaceStr) {
        str_replace(' ', '_', $namespaceStr);
        return $namespaceStr;
    }
}