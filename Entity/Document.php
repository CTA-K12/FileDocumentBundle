<?php

namespace Mesd\File\DocumentBundle\Entity;



use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Mesd\File\DocumentBundle\Entity\Document
 */
class Document {
    /**
     *
     *
     * @var integer $id
     */
    private $id;

    /**
     *
     *
     * @var string $filename
     */
    private $filename;

    /**
     *
     *
     * @var string $path
     */
    private $path;

    /**
     *
     *
     * @var string $mimetype
     */
    private $mimetype;

    /**
     *
     *
     * @var string $category
     */
    private $category;

    /**
     *
     *
     * @var string $hash
     */
    private $hash;

    private $file;

    private $temp;

    public static $dirs;

    /**
     * Get category
     *
     * @return string
     */
    public static function getDirs() {
        $vars=get_class_vars('Mesd\File\DocumentBundle\Entity\Document');
        $list=array_filter(array_keys($vars['dirs'])
            , function ($e) {return $e != 'default' && $e != 'temp';} );
        $dirs=array();
        foreach ($list as $key => $value) {
            $dirs[$value]=$value;
        }
        return $dirs;
    }

    public function __construct( $dir, $dirs ) {
        foreach ( $dirs as $key => $value ) {
            $this::$dirs[$key]=$dir.$value;
        }
        // var_dump($this::getCategories());die;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Default __toString.  Customize to suit
     */
    public function __toString() {
        return (string)$this->getId();
    }

    /**
     * Set filename
     *
     * @param string  $filename
     * @return Document
     */
    public function setFilename( $filename ) {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * Set path
     *
     * @param string  $path
     * @return Document
     */
    public function setPath( $path ) {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set mimetype
     *
     * @param string  $mimetype
     * @return Document
     */
    public function setMimetype( $mimetype ) {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * Get mimetype
     *
     * @return string
     */
    public function getMimetype() {
        return $this->mimetype;
    }

    /**
     * Set category
     *
     * @param string  $category
     * @return Document
     */
    public function setCategory( $category ) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     * Set hash
     *
     * @param string  $hash
     * @return DocumentedEntity
     */
    public function setHash( $hash ) {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Generate hash
     *
     * @param string  $hash
     * @return DocumentedEntity
     */
    public function generateHash( $data ) {
        $this->hash = hash( 'sha512', $data );

        return $this;
    }

    /**
     * Generate hash
     *
     * @param string  $filename
     * @return DocumentedEntity
     */
    public function generateHashFromFile( $filename ) {
        $this->hash = hash_file( 'sha512', $filename );

        return $this;
    }

    public function getFullName() {
        return $this->path.'/'.$this->hash;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile( $file = null ) {
        if('array' == gettype($file)){
            foreach($file as $single){
                    $this->file = $single;
                // check if we have an old image path
                if ( isset( $this->path ) ) {
                    // store the old name to delete after the update
                    $this->temp = $this->path;
                    $this->path = null;
                } else {
                    $this->path = ( $this->path ?: $this->getDir() );
                }
            }
        }
        else{
            $this->file = $file;
            // check if we have an old image path
            if ( isset( $this->path ) ) {
                // store the old name to delete after the update
                $this->temp = $this->path;
                $this->path = null;
            } else {
                $this->path = ( $this->path ?: $this->getDir() );
            }
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * life cycle cacllback: PrePersist/PreUpdate
     */
    public function preUpload() {
        if ( null !== $this->getFile() ) {
            $this->generateHashFromFile( $this->getFile() );
            $this->path = $this->getDir();
            $this->mimetype = $this->getFile()->guessExtension();
        }
        if ( !realpath( $this->path ) ) { mkdir( $this->path, 0770, true ); }
        $this->path=realpath( $this->path );
    }

    public function getDefaultDir() {
        return
        isset( $this::$dirs['default'] )
            ? $this::$dirs['default'].'default'
            : ''
        ;
    }

    public function getTempDir() {
        return isset( $this::$dirs['temp'] )
            ? $this::$dirs['temp']
            : $this->getDefaultDir();
    }

    public function getDir() {
        return ( isset( $this::$dirs[$this->category] )
            ? $this::$dirs[$this->category]
            : $this->getDefaultDir() )
            .( isset( $this->hash )
            ? '/'.substr( $this->hash, 0, 2 ).'/'.substr( $this->hash, 2, 2 ).'/'.substr( $this->hash, 4, 2 )
            : '' );
    }

    /**
     * life cycle cacllback: PreRemove
     */
    public function storeFilenameForRemove() {
        $this->temp = $this->getTempDir().'/'.$this->hash;
    }

    /**
     * life cycle cacllback: PostRemove
     */
    public function removeDocument() {
        $file = $this->getFullName();
        if ( $file && file_exists($file) ) {
            unlink( $file );
        }
    }

    /**
     * life cycle cacllback: PostPersist/PostUpdate
     */
    public function upload() {
        if ( null === $this->getFile() ) {
            return;
        }

        // var_dump( 'upload' );
        // var_dump( $this->path );
        // var_dump( $this->temp );
        // var_dump( 'get_dir' );
        // var_dump( $this->getDefaultDir() );
        // var_dump( $this->getDir() );
        // var_dump( $this->filename );
        // var_dump( $this->getFullName() );
        // var_dump( 'hash' );
        // var_dump( $this->hash );
        // var_dump( sys_get_temp_dir() );
        // var_dump($this);
        // die;

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        if ( !file_exists( $this->getFullName() ) ) {
            $this->getFile()->move( $this->getDir(), $this->getFullName() );
        }
        // check if we have an old image
        if ( isset( $this->temp ) ) {
            // delete the old image
            unlink( $this->getTempDir().'/'.$this->hash );
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    // this function is intended for a symfony application-generated file.

    public function write( $data ) {
        if ( isset( $this::$dirs[$this->getCategory()] ) ) {
            $this->path = $this::$dirs[$this->getCategory()];
        } else {
            $this->path = ( $this->path ?: getDefaultDir() );
        }

        $this->generateHash( $data );
        $this->path.='/'.substr( $this->hash, 0, 2 ).'/'.substr( $this->hash, 2, 2 ).'/'.substr( $this->hash, 4, 2 );
        if ( !realpath( $this->path ) ) { mkdir( $this->path, 0770, true ); }
        $this->path=realpath( $this->path );

        file_put_contents(
            $this->getFullName()
            , $data
        );
    }
}
