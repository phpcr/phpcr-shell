<?php

namespace PHPCR\Shell;

use PHPCR\SessionInterface;
use PHPCR\CredentialsInterface;
use PHPCR\Util\PathHelper;
use PHPCR\PathNotFoundException;

class PhpcrSession implements SessionInterface
{
    protected $session;
    protected $cwd = '/';

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

    public function autocomplete($text)
    {
        // get last string
        if (!preg_match('&^(.+) &', $text, $matches)) {
            return false;
        }

        $path = $matches[1];

        if (substr($path, 0, 1) == '/') {
            $parentPath = PathHelper::getParentPath($path);
            try {
                $node = $this->getNode($parentPath);
                $list = array();
                foreach ($node->getNodes() as $path => $node) {
                    $list[] = substr($parentPath, 1) . '/' . $path;
                }

                return $list;
            } catch (PathNotFoundException $e) {
                return false;
            }
        } else {
            $cwd = $this->getCwd();
            try {
                $node = $this->getNode($cwd);
                $list = array();
                foreach ($node->getNodes() as $path => $node) {
                    if ($this->getCwd() == '/') {
                        $list[] = $path;
                    } else {
                        $list[] = $path;
                    }
                }

                return $list;
            } catch (PathNotFoundException $e) {
                return false;
            }
        }
    }

    public function chdir($path)
    {
        $cwd = $this->getCwd();

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

        $this->setCwd($newPath);
    }

    public function getAbsPath($path)
    {
        if (!$path) {
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

    public function getAbsPaths($paths)
    {
        $newPaths = array();
        foreach ($paths as $path) {
            $newPaths[] = $this->getAbsPath($path);
        }

        return $newPaths;
    }

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
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
        return $this->session->getNodesByIdentifier($id);
    }

    public function getItem($path)
    {
        return $this->session->getItem($this->getAbsPath($path));
    }

    public function getNode($path, $depthHint = -1)
    {
        return $this->session->getNode($this->getAbsPath($path));
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

    public function move($srcAbsPath, $destAbsPath)
    {
        return $this->session->move($this->getAbsPath($srcAbsPath), $this->getAbsPath($destAbsPath));
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

    public function hasCapability($methodName, $target, array $arguments)
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
}
