<?php

namespace Mesd\FileDocumentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root( 'mesd_file_document' );

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->arrayNode('dirs')
                ->useAttributeAsKey('dirs')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
        // max_file_uploads:       5   (-1 default)
        // max_file_size_upload:   2Mb (-1 MB default)
        // min_file_size_upload:   1Kb (-1 KB default)
        // max_total_size_upload:  2Mb (-1 MB default)
        // file_types_whitelist:   {png, jpg, pdf, tif} ({*} default)
        // file_types_blacklist:   {exe, rar}           ({exe, so} default)
        // allow_compressions:     {zip, rar, tgz}
        // permissions:            0770 default
        // 
        // save_as_filename:       %year%/%month%/%filename%.%ext% if(isdup(%this%),'(%dup%)','') default
        // example as hash:        %hash%|substr(0,1)/substr(%hash%,1,1)/%hash%.%ext%
        // !!! options for filename configuration
            // %filename%           file name
            // %ext%                file format extension
            // %year%               4 digit year YYYY
            // %month%              2 digit month 01-12
            // %day%                2 digit day 01-31
            // %minute%             2 digit minute 01-60
            // %hour%               2 digit hour 00-23
            // %hash%               32 char hash of file
            // %docid%              document id
            // %userid%             user id
            // %dup%                for use when duplicates are allowed, first digit is 1
            // %this%               non-printable, used for calculations
            // |substr(0,0)         substring, first character, number of characters, negative for reverse
            // |camel()             camelize a string   iAmLegend
            // |underscore()        underscore a string i_am_legend
            // |dash()              dash a string       i-am-legend
            // |max(3)              maximum letters allowed
            // |min(1)              minimum letters allowed
            // |upper()             uppercase a string
            // |lower()             lowercase a string
            // if($test, $yes, $no) if test
            // isDup()              bool return values, is duplicate
            // isType(string|array) bool return values, is type (file format type)

        return $treeBuilder;
    }
}
