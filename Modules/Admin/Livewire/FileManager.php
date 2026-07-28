<?php

namespace Modules\Admin\Livewire;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Flysystem\FilesystemException;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[title('مدیریت فایل‌ها')]
class FileManager extends Component
{

    use WithFileUploads;

    public $uploadFile;

    public $broadcamp = array();
    public $files = array();

    public string $directoryName = '';

    public string $fileToRename = '';
    public string $renameTo = '';


    public function deleteDirectory($directory)
    {
        //delete directory recursive
        $files = array_diff(scandir($directory), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$directory/$file")) ? $this->deleteDirectory("$directory/$file") : unlink("$directory/$file");
        }
        return rmdir($directory);
    }

    #[On('delete')]
    public function delete($file): void
    {
        $path = implode('/', array_column($this->broadcamp, 'path')).'/'.$file;
        //check if file is directory
        if (Storage::disk('public')->directoryExists($path)) {
            //delete directory recursive
            Storage::disk('public')->deleteDirectory($path);
        } else {
            Storage::disk('public')->delete($path);
        }
        $this->files = $this->listFiles();
    }

    public function goToDirectory($folder)
    {
        //check directory exist
        $current = implode('/', array_column($this->broadcamp, 'path'));
        if (Storage::disk('public')->directoryExists($current . '/' . $folder)) {
            $this->broadcamp[] = array(
                'title' => $folder,
                'path' =>  $folder,
            );
            $this->files = $this->listFiles();
        } else {
            $this->dispatch('error_file_manager', message:'این پوشه وجود ندارد');
        }
    }

    public function listFiles(): array
    {
        $icons = [
            'png' => admin_asset('images/files/png.png'),
            'pdf' => admin_asset('images/files/pdf.png'),
            'ppt' => admin_asset('images/files/ppt.png'),
            'jpg' => admin_asset('images/files/image.png'),
            'gif' => admin_asset('images/files/image.png'),
            'jpeg' => admin_asset('images/files/image.png'),
            'zip' => admin_asset('images/files/zip.png'),
            'xlsx' => admin_asset('images/files/xlsx.png'),
            'xls' => admin_asset('images/files/xlsx.png'),
            'doc' => admin_asset('images/files/doc.png'),
            'docx' => admin_asset('images/files/doc.png'),
            'other' => admin_asset('images/files/file.png'),
        ];
        $files = array();
        $folders = array();
        $currentPath = implode('/', array_column($this->broadcamp, 'path'));
        $fileInDirectory = Storage::disk('public')->files($currentPath);
        $directories = Storage::disk('public')->directories($currentPath);
        //add directories to folders
        foreach ($directories as $directory) {
            $folders[] = pathinfo($directory, PATHINFO_BASENAME);
        }
        foreach ($fileInDirectory as $item) {
            $size = 0;
            try {
                $size = Storage::disk('public')->fileSize($item);
            } catch (FilesystemException $ignore) {
            }
            $files[] = [
                'name' => pathinfo($item, PATHINFO_FILENAME).'.'.pathinfo($item, PATHINFO_EXTENSION),
                'pure_name' => pathinfo($item, PATHINFO_FILENAME),
                'extension' => pathinfo($item, PATHINFO_EXTENSION),
                'size' => $this->readableFileSize($size),
                'url' => Storage::disk('public')->url($item),
                'icon' => isset($icons[pathinfo($item, PATHINFO_EXTENSION)]) ? $icons[pathinfo($item,
                    PATHINFO_EXTENSION)] : $icons['other'],
            ];
        }
        return array(
            'files' => $files,
            'folders' => $folders,
        );
    }

    public function createDirectory(): void
    {
        $validatedData = Validator::make(
            ['directoryName' => $this->directoryName],
            ['directoryName' => 'required']);
        if (!$validatedData->fails()) {
            $current = implode('/', array_column($this->broadcamp, 'path'));
            Storage::disk('public')->makeDirectory($current . '/' . $this->directoryName);
            $this->files = $this->listFiles();
            $this->directoryName = '';
            $this->dispatch('close_modal_directory');
        } else {
            $this->dispatch('error_file_manager', message:'نام پوشه را وارد کنید');
        }
    }

    public function rename(): void
    {
        $validatedData = Validator::make(
            ['renameTo' => $this->renameTo, 'fileToRename' => $this->fileToRename],
            ['renameTo' => 'required', 'fileToRename' => 'required']);
        if (!$validatedData->fails()) {
            $current = implode('/', array_column($this->broadcamp, 'path'));
            //check if is file extension should not change
            if (Storage::disk('public')->fileExists($current . '/' . $this->fileToRename)) {
                //get before and after file extension
                $before = pathinfo($this->fileToRename, PATHINFO_EXTENSION);
                $after = pathinfo($this->renameTo, PATHINFO_EXTENSION);
                if ($before != $after) {
                    $this->dispatch('error_file_manager', message:'امکان تغییر پسوند فایل وجود ندارد');
                    return;
                }
            }
            Storage::disk('public')->move($current . '/' . $this->fileToRename, $current . '/' . $this->renameTo);
            $this->files = $this->listFiles();
            $this->fileToRename = '';
            $this->renameTo = '';
            $this->dispatch('close_modal_rename');
        } else {
            $this->dispatch('error_file_manager', message:'لطفا نام جدید را وارد کنید');
        }
    }

    public function mount()
    {
        $this->broadcamp = array(
            array(
                'title' => 'خانه',
                'path' => '',
            )
        );
        //file list files and folders in path
        $this->files = $this->listFiles();
        //aviable icons
    }

    public function goTo($index): void
    {
        $this->broadcamp = array_slice($this->broadcamp, 0, $index + 1);
        $this->files = $this->listFiles($this->broadcamp[$index]['path']);
    }

    public function render()
    {
        return view('admin::livewire.file-manager');
    }

    #[On('upload')]
    public function upload()
    {
        $validatedData = Validator::make(
            ['uploadFile' => $this->uploadFile],
            ['uploadFile' => 'required']);
        if ($validatedData->fails()) {
            $this->dispatch('error_file_manager', message:'لطفا فایل را انتخاب کنید');
            return;
        }
        //upload file to current directory
        $current = implode('/', array_column($this->broadcamp, 'path'));
        $this->uploadFile->store($current, 'public');
        $this->files = $this->listFiles();
        $this->dispatch('upload_complete');
    }

    public function readableFileSize($fileSize): string
    {
        if ($fileSize > 0) {
            $i = floor(log($fileSize, 1024));
            return round($fileSize / pow(1024, $i), 2) . ' ' . ['B', 'KB', 'MB', 'GB', 'TB'][$i];
        }
        return $fileSize;
    }
}
