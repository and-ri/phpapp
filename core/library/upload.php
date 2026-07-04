<?php

/**
 * Safe handling of uploaded files:
 *
 *   $result = $this->upload->save($this->request->files['avatar'], [
 *       'dir'        => DIR_DATA . 'uploads/',
 *       'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
 *       'max_size'   => 2 * 1024 * 1024
 *   ]);
 *
 *   if ($result) { ... $result['path'] ... } else { ... $this->upload->errors() ... }
 *
 * The stored file always gets a random name; the real MIME type is
 * checked with finfo, and script extensions are never allowed.
 */
class Upload {
    protected $errors = array();

    // Never stored, whatever the caller allows
    protected $forbidden = array('php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'cgi', 'pl', 'sh', 'asp', 'aspx', 'jsp', 'htaccess');

    // Known extension => acceptable MIME types
    protected $mime_map = array(
        'jpg' => array('image/jpeg'),
        'jpeg' => array('image/jpeg'),
        'png' => array('image/png'),
        'gif' => array('image/gif'),
        'webp' => array('image/webp'),
        'svg' => array('image/svg+xml'),
        'pdf' => array('application/pdf'),
        'txt' => array('text/plain'),
        'csv' => array('text/csv', 'text/plain'),
        'zip' => array('application/zip'),
        'mp3' => array('audio/mpeg'),
        'mp4' => array('video/mp4'),
        'doc' => array('application/msword'),
        'docx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        'xls' => array('application/vnd.ms-excel'),
        'xlsx' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
    );

    public function save($file, $options = array()) {
        $this->errors = array();

        $dir = isset($options['dir']) ? $options['dir'] : DIR_DATA . 'uploads/';
        $extensions = isset($options['extensions']) ? array_map('strtolower', $options['extensions']) : array('jpg', 'jpeg', 'png', 'gif', 'webp');
        $max_size = isset($options['max_size']) ? (int)$options['max_size'] : 5 * 1024 * 1024;

        if (!is_array($file) || !isset($file['tmp_name'])) {
            $this->errors[] = 'No file was uploaded.';
            return false;
        }

        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->uploadErrorMessage($file['error']);
            return false;
        }

        if ($file['size'] > $max_size) {
            $this->errors[] = 'The file exceeds the maximum allowed size.';
            return false;
        }

        $original = basename((string)$file['name']);
        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));

        if (!$extension || in_array($extension, $this->forbidden) || !in_array($extension, $extensions)) {
            $this->errors[] = 'The file type is not allowed.';
            return false;
        }

        // From here on the file itself is touched: make sure it really
        // came through an HTTP upload before reading it
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->errors[] = 'Invalid upload.';
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (isset($this->mime_map[$extension]) && !in_array($mime, $this->mime_map[$extension])) {
            $this->errors[] = 'The file content does not match its extension.';
            return false;
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = bin2hex(random_bytes(16)) . '.' . $extension;
        $path = rtrim($dir, '/') . '/' . $name;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            $this->errors[] = 'Could not store the uploaded file.';
            return false;
        }

        chmod($path, 0644);

        return array(
            'name' => $name,
            'path' => $path,
            'original' => $original,
            'extension' => $extension,
            'mime' => $mime,
            'size' => $file['size'],
        );
    }

    public function errors() {
        return $this->errors;
    }

    protected function uploadErrorMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'The file exceeds the maximum allowed size.';
            case UPLOAD_ERR_PARTIAL:
                return 'The file was only partially uploaded.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            default:
                return 'The file could not be uploaded.';
        }
    }
}
