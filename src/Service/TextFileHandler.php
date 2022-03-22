<?php
// src/Service/TextFileHandler.php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class TextFileHandler
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    /**
     * Upload Text File
     */
    public function upload(UploadedFile $file) : string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            (new LoggerInterface)->info($e);

            throw new FileException("Upload Failed");
        }

        return $fileName;
    }

    /**
     * Read File content to get Palindromes
     */
    public function searchPalindrome(string $path) : array
    {
        $file = new SplFileInfo($path, '', '');
        $contents = $file->getContents();

        return $this->getPalindromes($contents);
    }

    /**
     * Get count and strings of palindromes
     */
    public function getPalindromes(string $string) : array
    { 
        $new_string = str_replace('', ' ', $string);
        $new_string = preg_replace('/[^A-Za-z0-9\-]/', ' ', $new_string);
        $words = explode(' ', $new_string);
        $count = 0;
        $palindrome_strings = [];

        foreach($words as $word) {
            $word = strtolower($word);

            if(strrev($word) === $word) {
                $palindrome_strings[] = $word;
                $count++;
            }
        }
         
        return [$string, $count, $palindrome_strings];
    }

    public function getTargetDirectory() : string
    {
        return $this->targetDirectory;
    }
}