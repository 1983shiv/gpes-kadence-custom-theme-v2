<?php

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_FILES['file'])) {
          $uploadedFile = $_FILES['file'];
          var_dump($uploadedFile);
          // Define allowed file extensions and max file size
          $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
          $maxFileSize = 1 * 1024 * 1024; // 1 MB

          // Get file extension and size
          $fileName = $uploadedFile['name'];
          $fileSize = $uploadedFile['size'];
          $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

          // Validate file extension
          if (!in_array($fileExtension, $allowedExtensions)) {
              http_response_code(400);
              echo 'File extension is not allowed.';
              exit;
          }

          // Validate file size
          if ($fileSize > $maxFileSize) {
              http_response_code(400);
              echo 'File size exceeds the allowed limit.';
              exit;
          }

          // Set destination path within WordPress uploads directory
          $uploadDir = wp_upload_dir();
          $uploadPath = $uploadDir['path'] . '/' . $fileName;

          // Move the uploaded file to the destination
          if (move_uploaded_file($uploadedFile['tmp_name'], $uploadPath)) {
              // Optionally, you can perform further processing here

              // Return a success response
              echo 'File uploaded successfully.';
              exit;
          } else {
              http_response_code(500);
              echo 'Error uploading file.';
              exit;
          }
      }
  }


?>