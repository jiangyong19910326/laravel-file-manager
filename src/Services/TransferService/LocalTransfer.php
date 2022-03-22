<?php

namespace Alexusmai\LaravelFileManager\Services\TransferService;

use Alexusmai\LaravelFileManager\Traits\PathTrait;
use Storage;

class LocalTransfer extends Transfer
{
    use PathTrait;

    /**
     * LocalTransfer constructor.
     *
     * @param $disk
     * @param $path
     * @param $clipboard
     */
    public function __construct($disk, $path, $clipboard)
    {
        // dump($disk,$path,$clipboard);
        parent::__construct($disk, $path, $clipboard);
        // dd($path,$clipboard);
    }

    /**
     * Copy files and folders
     */
    protected function copy()
    {
        foreach ($this->clipboard['files'] as $file) {
            Storage::disk($this->disk)->copy(
                $file,
                $this->path,
            );
        }
        if($this->clipboard['directories'] == null)
        {
            $this->clipboard['directories'] = [];
        }
        // directories
        foreach ($this->clipboard['directories'] as $directory) {
            $this->copyDirectory($directory);
        }
    }

    /**
     * Cut files and folders
     */
    protected function cut()
    {
        // files
        foreach ($this->clipboard['files'] as $file) {
            Storage::disk($this->disk)->copy(
                $file,
                // $this->renamePath($file, $this->path)
                $this->path, //seaweedfs
            );
        }
        // directories
        if($this->clipboard['directories'] == null)
        {
            $this->clipboard['directories'] = [];
        }
        foreach ($this->clipboard['directories'] as $directory) {
            Storage::disk($this->disk)->copy(
                $directory,
                // $this->renamePath($directory, $this->path)
                $this->path,//seaweedfs
            );
        }
      
    }

    /**
     * Copy directory
     *
     * @param $directory
     */
    protected function copyDirectory($directory)
    {
        // get all directories in this directory
        $allDirectories = Storage::disk($this->disk)
            ->allDirectories($directory);

        $partsForRemove = count(explode('/', $directory)) - 1;
        
        // create this directories
        foreach ($allDirectories as $dir) {
            Storage::disk($this->disk)->makeDirectory(
                $this->transformPath(
                    $dir,
                    $this->path,
                    $partsForRemove
                )
            );
        }

        // get all files
        $allFiles = Storage::disk($this->disk)->allFiles($directory);
        // copy files
        foreach ($allFiles as $file) {
            Storage::disk($this->disk)->copy(
                $file,
                $this->transformPath($file, $this->path, $partsForRemove)
            );
        }
    }
}
