<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Phpcr;

use PHPCR\SessionInterface;
use PHPCR\CredentialsInterface;
use PHPCR\Util\UUIDHelper;
use PHPCR\PathNotFoundException;
use DTL\Glob\Finder\PhpcrTraversalFinder;

/**
 * Custom session wrapper for PHPCR Shell
 *
 * Supports current-working-directory etc.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PhpcrSession implements SessionInterface
{
    protected $session;
    protected $cwd = '/';
    protected $finder;

    public function __construct(SessionInterface $session, $finder = null)
    {
        $this->session = $session;
        $this->finder = $finder ?: new PhpcrTraversalFinder($this);
    }

    /**
     * Allow underlying session to be changed
     * For example when changing workspaces
     *
     * @param SessionInterface $session
     */
    public function setPhpcrSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getCurrentNode()
    {
        return $this->getNode($this->getCwd());
    }

    public function getCwd()
    {
        return $this->cwd;
    }

    public function setCwd($cwd)
    {
        $this->cwd = $cwd;
    }

    /**
     * @TODO: Refactor this.
     */
    public function autocomplete($text)
    {
        // return autocompletions for current path
        $cwd = $this->getCwd();
        try {
            $node = $this->getNode($cwd);
            $list = (array) $node->getNodeNames();
            foreach ($node->getProperties() as $name => $v) {
                $list[] = $name;
            }

            return $list;
        } catch (PathNotFoundException $e) {
            return false;
        }
    }

    public function chdir($path)
    {
        $cwd = $this->getCwd();

        if (UUIDHelper::isUUID($path)) {
            $node = $this->getNodeByIdentifier($path);
            $newPath = $node->getPath();
        } else {
            // absolute path
            if (substr($path, 0, 1) == '/') {
                $newPath = $path;
            } elseif ($path == '..') {
                $newPath = dirname($cwd);
            } else {
                if ($this->cwd == '/') {
                    $newPath = sprintf('/%s', $path);
                } else {
                    $newPath = sprintf('%s/%s', $cwd, $path);
                }
            }

            // check that path is valid
            $this->getNode($newPath);
        }

        $this->setCwd($newPath);
    }

    public function getAbsPath($path)
    {
        if (!$path || $path === '.') {
            return $this->getCwd();
        }

        if (substr($path, 0, 1) == '/') {
            $absPath = $path;
        } else {
            if ($this->cwd == '/') {
                $absPath = sprintf('/%s', $path);
            } else {
                $absPath = sprintf('%s/%s', $this->getCwd(), $path);
            }
        }

        return $absPath;
    }

    /**
     * Infer the absolute target path for a given source path.
     *
     * This means that if there is a node at targetPath then we
     * will return append the  basename of $srcPath to $targetPath.
     *
     * @param string $srcPath
     * @param string $targetPath
     *
     * @return string
     */
    public function getAbsTargetPath($srcPath, $targetPath)
    {
        $targetPath = $this->getAbsPath($targetPath);

        try {
            $this->getNode($targetPath);
        } catch (PathNotFoundException $e) {
            return $targetPath;
        }

        $basename = basename($this->getAbsPath($srcPath));

        return $this->getAbsPath(sprintf('%s/%s', $targetPath, $basename));
    }

    public function getAbsPaths($paths)
    {
        $newPaths = array();
        foreach ($paths as $path) {
            $newPaths[] = $this->getAbsPath($path);
        }

        return $newPaths;
    }

    /**
     * If the given parameter looks like a UUID retrieve
     * by Identifier, otherwise by path.
     *
     * @param string $pathOrId
     *
     * @return NodeInterface
     *
     * @throws PathNotFoundException if no accessible node is found at the specified path.
     * @throws ItemNotFoundException if no node with the specified
     *                               identifier exists or if this Session does not have read access to
     *                               the node with the specified identifier.
     */
    public function getNodeByPathOrIdentifier($pathOrId)
    {
        if (true === UUIDHelper::isUUID($pathOrId)) {
            return $this->getNodeByIdentifier($pathOrId);
        }

        $pathOrId = $this->getAbsPath($pathOrId);

        return $this->getNode($pathOrId);
    }

    public function getRepository()
    {
        return $this->session->getRepository();
    }

    public function getUserID()
    {
        return $this->session->getUserID();
    }

    public function getAttributeNames()
    {
        return $this->session->getAttributeNames();
    }

    public function getAttribute($name)
    {
        return $this->session->getAttribute($name);
    }

    public function getWorkspace()
    {
        return $this->session->getWorkspace();
    }

    public function getRootNode()
    {
        return $this->session->getRootNode();
    }

    public function impersonate(CredentialsInterface $credentials)
    {
        return $this->session->impersonate($credentials);
    }

    public function getNodeByIdentifier($id)
    {
        return $this->session->getNodeByIdentifier($id);
    }

    public function getNodesByIdentifier($ids)
    {
        return $this->session->getNodesByIdentifier($ids);
    }

    public function getItem($path)
    {
        return $this->session->getItem($this->getAbsPath($path));
    }

    public function getNode($path, $depthHint = -1)
    {
        return $this->session->getNode($this->getAbsPath($path), $depthHint);
    }

    public function getNodes($paths)
    {
        return $this->session->getNodes($this->getAbsPaths($paths));
    }

    public function getProperty($path)
    {
        return $this->session->getProperty($this->getAbsPath($path));
    }

    public function getProperties($paths)
    {
        return $this->session->getProperties($this->getAbsPaths($paths));
    }

    public function itemExists($path)
    {
        return $this->session->itemExists($this->getAbsPath($path));
    }

    public function nodeExists($path)
    {
        return $this->session->nodeExists($this->getAbsPath($path));
    }

    public function propertyExists($path)
    {
        return $this->session->propertyExists($this->getAbsPath($path));
    }

    public function move($srcPath, $destPath)
    {
        return $this->session->move($this->getAbsPath($srcPath), $this->getAbsTargetPath($srcPath, $destPath));
    }

    public function removeItem($path)
    {
        return $this->session->removeItem($this->getAbsPath($path));
    }

    public function save()
    {
        return $this->session->save();
    }

    public function refresh($keepChanges)
    {
        return $this->session->refresh($keepChanges);
    }

    public function hasPendingChanges()
    {
        return $this->session->hasPendingChanges();
    }

    public function hasPermission($path, $actions)
    {
        return $this->session->hasPermission($this->getAbsPath($path), $actions);
    }

    public function checkPermission($path, $actions)
    {
        return $this->session->checkPermission($this->getAbsPath($path), $actions);
    }

    public function hasCapability($methodNames, $target, array $arguments)
    {
        return $this->session->hasCapability($methodNames, $target, $arguments);
    }

    public function importXML($parentAbsPath, $uri, $uuidBehavior)
    {
        return $this->session->importXML($this->getAbsPath($parentAbsPath), $uri, $uuidBehavior);
    }

    public function exportSystemView($path, $stream, $skipBinary, $noRecurse)
    {
        return $this->session->exportSystemView($this->getAbsPath($path), $stream, $skipBinary, $noRecurse);
    }

    public function exportDocumentView($path, $stream, $skipBinary, $noRecurse)
    {
        return $this->session->exportDocumentView($this->getAbsPath($path), $stream, $skipBinary, $noRecurse);
    }

    public function setNamespacePrefix($prefix, $uri)
    {
        return $this->session->setNamespacePrefix($prefix, $uri);
    }

    public function getNamespacePrefixes()
    {
        return $this->session->getNamespacePrefixes();
    }

    public function getNamespaceURI($prefix)
    {
        return $this->session->getNamespaceURI($prefix);
    }

    public function getNamespacePrefix($uri)
    {
        return $this->session->getNamespacePrefix($uri);
    }

    public function logout()
    {
        return $this->session->logout();
    }

    public function isLive()
    {
        return $this->session->isLive();
    }

    public function getAccessControlManager()
    {
        return $this->session->getAccessControlManager();
    }

    public function getRetentionManager()
    {
        return $this->session->getRetentionManager();
    }

    public function findNodes($patternOrId)
    {
        if (true === UUIDHelper::isUUID($patternOrId)) {
            return $this->getNodeByIdentifier($patternOrId);
        }

        $res = $this->finder->find($this->getAbsPath($patternOrId));

        return $res;
    }
}
