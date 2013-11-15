<?php

namespace BlogBundle\Event;

class Blog
{
    /**
     * Is triggered only when new blog is created.
    **/
    const IS_CREATED = 'is_blog_created';

    /**
     * Is triggered when blog is beeing deleted.
    **/
    const IS_DESTROYED = 'is_blog_destroyed';

    /**
     * Is trigered when blog is edited.
    **/
    const IS_EDITED = 'is_blog_edited';
    
    /**
     * Is trigered when new post is created to blog.
    **/
    const IS_NEW_POST_CREATED = 'is_new_post_created';
    
    /**
     * Is trigered when new post is created to blog.
    **/
    const IS_POST_DESTROYED = 'is_post_destroyed';
    
    /**
     * Is trigered when post is edited.
    **/
    const IS_POST_EDITED = 'is_post_edited';
}
