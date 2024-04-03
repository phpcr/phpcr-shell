<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PHPCR\Shell\Phpcr;

use DTL\Glob\Finder\PhpcrTraversalFinder;
use PHPCR\CredentialsInterface;
use PHPCR\ItemNotFoundException;
use PHPCR\NodeInterface;
use PHPCR\PathNotFoundException;
use PHPCR\SessionInterface;
use PHPCR\Util\UUIDHelper;

/**
 * Custom session wrapper for PHPCR Shell.
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
     * For example when changing workspaces.
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
            if (str_starts_with($path, '/')) {
                $newPath = $path;
            } elseif ($path === '..') {
                $newPath = dirname($cwd);
            } elseif ($this->cwd === '/') {
                $newPath = sprintf('/%s', $path);
            } else {
                $newPath = sprintf('%s/%s', $cwd, $path);
            }

            if ($newPath !== '/') {
                $newPath = rtrim($newPath, '/');
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

        if (str_starts_with($path, '/')) {
            $absPath = $path;
        } elseif ('/' === $this->cwd) {
            $absPath = sprintf('/%s', $path);
        } else {
            $absPath = sprintf('%s/%s', $this->getCwd(), $path);
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
        $newPaths = [];
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
     * @throws PathNotFoundException if no accessible node is found at the specified path.
     * @throws ItemNotFoundException if no node with the specified
     *                               identifier exists or if this Session does not have read access to
     *                               the node with the specified identifier.
     *
     * @return NodeInterface
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

    public function getItem($absPath)
    {
        return $this->session->getItem($this->getAbsPath($absPath));
    }

    public function getNode($absPath, $depthHint = -1)
    {
        return $this->session->getNode($this->getAbsPath($absPath), $depthHint);
    }

    public function getNodes($absPaths)
    {
        return $this->session->getNodes($this->getAbsPaths($absPaths));
    }

    public function getProperty($absPath)
    {
        return $this->session->getProperty($this->getAbsPath($absPath));
    }

    public function getProperties($absPaths)
    {
        return $this->session->getProperties($this->getAbsPaths($absPaths));
    }

    public function itemExists($absPath)
    {
        return $this->session->itemExists($this->getAbsPath($absPath));
    }

    public function nodeExists($absPath)
    {
        return $this->session->nodeExists($this->getAbsPath($absPath));
    }

    public function propertyExists($absPath)
    {
        return $this->session->propertyExists($this->getAbsPath($absPath));
    }

    public function move($srcAbsPath, $destAbsPath)
    {
        return $this->session->move($this->getAbsPath($srcAbsPath), $this->getAbsTargetPath($srcAbsPath, $destAbsPath));
    }

    public function removeItem($absPath)
    {
        return $this->session->removeItem($this->getAbsPath($absPath));
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

    public function hasPermission($absPath, $actions)
    {
        return $this->session->hasPermission($this->getAbsPath($absPath), $actions);
    }

    public function checkPermission($absPath, $actions)
    {
        return $this->session->checkPermission($this->getAbsPath($absPath), $actions);
    }

    public function hasCapability($methodName, $target, array $arguments)
    {
        return $this->session->hasCapability($methodName, $target, $arguments);
    }

    public function importXML($parentAbsPath, $uri, $uuidBehavior)
    {
        return $this->session->importXML($this->getAbsPath($parentAbsPath), $uri, $uuidBehavior);
    }

    public function exportSystemView($absPath, $stream, $skipBinary, $noRecurse)
    {
        return $this->session->exportSystemView($this->getAbsPath($absPath), $stream, $skipBinary, $noRecurse);
    }

    public function exportDocumentView($absPath, $stream, $skipBinary, $noRecurse)
    {
        return $this->session->exportDocumentView($this->getAbsPath($absPath), $stream, $skipBinary, $noRecurse);
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

        return $this->finder->find($this->getAbsPath($patternOrId));
    }
}
