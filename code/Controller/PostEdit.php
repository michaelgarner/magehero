<?php

class Controller_PostEdit extends Controller_Account
{
    public function _preDispatch()
    {
        parent::_preDispatch();
    }


    public function get($postId)
    {
        $this->_preDispatch();

        $post = $this->_getContainer()->Post()->load($postId);
        if ($this->_getCurrentUser()->getId() != $post->getUserId()) {
            die("Permission denied");
        }

        $tags = $this->_getContainer()->Tag()->fetchAll();

        if ($this->_getCurrentUser()->getId() != $post->getUserId()) {
            die("Permission denied");
        }

        echo $this->_getTwig()->render('post_edit.html.twig', array(
            'session'       => $this->_getSession(),
            'post'          => $post,
            'local_config'  => $this->_getContainer()->LocalConfig(),
            'tags'          => $tags,
        ));
    }

    public function post($postId)
    {
        $imageUrl = isset($_POST['image_url']) ? $_POST['image_url'] : null;
        $subject = isset($_POST['subject']) ? $_POST['subject'] : null;
        $body = isset($_POST['body']) ? $_POST['body'] : null;
        $tagIds = isset($_POST['tag_ids']) ? $_POST['tag_ids'] : null;
        $isActive = isset($_POST['is_active']) ? $_POST['is_active'] : null;
        $isNews = isset($_POST['is_news']) ? $_POST['is_news'] : null;

        if ($imageUrl) {
            if (strpos($imageUrl, "javascript:") !== false || strpos($imageUrl, "data:") !== false) {
                die("Looks like an injection attempt");
            }
        }

        if (! $tagIds || empty($tagIds)) {
            die("You have to pick at least one tag");
        }

        $post = $this->_getContainer()->Post()->load($postId);
        if ($this->_getCurrentUser()->getId() != $post->getUserId()) {
            die("Permission denied");
        }

        $post->set('subject', $subject)
            ->set('body', $body)
            ->set('tag_ids', $tagIds)
            ->set('name', isset($profileData['name']) ? $profileData['name'] : null)
            ->set('is_active', (int)$isActive)
            ->set('is_news', (int)$isNews)
            ->set('image_url', $imageUrl)
            ->save();

        $this->_redirect($post->getUrl());
    }

}