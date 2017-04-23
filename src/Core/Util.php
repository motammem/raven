<?php

/*
* This file is part of the raven package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Raven\Core;

class Util
{
    public static function urljoin($base, $rel)
    {
        $pbase = parse_url($base);
        $prel = parse_url($rel);
        $merged = array_merge($pbase, $prel);
        if ($prel['path'][0] != '/') {
            // Relative path
            $dir = preg_replace('@/[^/]*$@', '', $pbase['path']);
            $merged['path'] = $dir . '/' . $prel['path'];
        }
        // Get the path components, and remove the initial empty one
        $pathParts = explode('/', $merged['path']);
        array_shift($pathParts);
        $path = [];
        $prevPart = '';
        foreach ($pathParts as $part) {
            if ($part == '..' && count($path) > 0) {
                // Cancel out the parent directory (if there's a parent to cancel)
                $parent = array_pop($path);
                // But if it was also a parent directory, leave it in
                if ($parent == '..') {
                    array_push($path, $parent);
                    array_push($path, $part);
                }
            } elseif ($prevPart != '' || ($part != '.' && $part != '')) {
                // Don't include empty or current-directory components
                if ($part == '.') {
                    $part = '';
                }
                array_push($path, $part);
            }
            $prevPart = $part;
        }
        $merged['path'] = '/' . implode('/', $path);
        $ret = '';
        if (isset($merged['scheme'])) {
            $ret .= $merged['scheme'] . ':';
        }
        if (isset($merged['scheme']) || isset($merged['host'])) {
            $ret .= '//';
        }
        if (isset($prel['host'])) {
            $hostSource = $prel;
        } else {
            $hostSource = $pbase;
        }
        // username, password, and port are associated with the hostname, not merged
        if (isset($hostSource['host'])) {
            if (isset($hostSource['user'])) {
                $ret .= $hostSource['user'];
                if (isset($hostSource['pass'])) {
                    $ret .= ':' . $hostSource['pass'];
                }
                $ret .= '@';
            }
            $ret .= $hostSource['host'];
            if (isset($hostSource['port'])) {
                $ret .= ':' . $hostSource['port'];
            }
        }
        if (isset($merged['path'])) {
            $ret .= $merged['path'];
        }
        if (isset($prel['query'])) {
            $ret .= '?' . $prel['query'];
        }
        if (isset($prel['fragment'])) {
            $ret .= '#' . $prel['fragment'];
        }
        return $ret;
    }
}
