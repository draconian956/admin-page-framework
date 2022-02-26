<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Resource_taxonomy_field extends AdminPageFramework_Resource_post_meta_box {
    protected function _enqueueSRCByCondition($aEnqueueItem)
    {
        $this->_enqueueSRC($aEnqueueItem);
    }
}
