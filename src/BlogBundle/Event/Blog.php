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
}
