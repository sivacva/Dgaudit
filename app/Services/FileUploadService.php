<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Models\FileUpload;
use Illuminate\Http\UploadedFile;
use DB;
use Illuminate\Support\Facades\View;


class FileUploadService
{
    /**
     * Handle file upload, compression, validation, and logging.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $destinationPath
     * @return array
     */
       public function uploadFile(UploadedFile $file, string $destinationPath, $fileUploadId, $destinationpathArray)
    {

        // Step 1: Validate the file (Ensure the file is valid before proceeding)
        $validationResult = $this->validateFile($file);
        if ($validationResult !== true) {
            return $validationResult;  // Return validation errors if the file is not valid
        }

        // Optional Step: Compress the image if it's an image (uncomment to use)
        // if ($file->isImage()) {
        //     $file = $this->compressImage($file);
        // }

        // Step 2: Modify the filename by removing hyphens
        $originalName = $file->getClientOriginalName();
        //$modifiedName = str_replace('-', '', $originalName);

        $modifiedName = preg_replace('/[\s\-,]+/', '', $originalName);

        $hashedName = hash('sha256', $modifiedName);

        // Step 3: Get the first 20 characters of the hash to limit the length
        $encryptedName = substr($hashedName, 0, 20);  // Get only the first 20 characters

        // Step 4: Get the original file extension
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);

        // Combine the encrypted name with the extension
        $encryptedNameWithExtension = $encryptedName . '.' . $fileExtension;



        if (isset($destinationpathArray)) {
           $uploadfolder = View::shared('uploadsfilefoldername');
           $currentPath = $uploadfolder; // Use storage_path here
 		//$currentPath = '';
            // Iterate over the path parts and check if each folder exists
            foreach ($destinationpathArray as $part) {
                $currentPath .= DIRECTORY_SEPARATOR . $part; // Use DIRECTORY_SEPARATOR for compatibility

                // Check if the current part exists, if not, create it
               // if (!is_dir($currentPath)) {
                    // Folder doesn't exist, create it
                  //  if (!mkdir($currentPath, 0777, true)) {
                       // echo "Failed to create folder: $currentPath\n";
                  //  }
              //  }
            }

            $destinationPath   =   $currentPath;
//echo $destinationPath;
//exit;
        }
        // Step 3: Store the file in the specified folder
        // Store the file and retrieve the path with the modified filename
        $filePath = $this->storeFile($file, $destinationPath, $encryptedNameWithExtension);
        // Ensure the file is stored correctly, check if $filePath is not empty
        if (empty($filePath)) {
            return response()->json(['error' => 'File upload failed.'], 500);  // Error if file upload failed
        }

        // Step 4: Insert or Update the file details into the database
        // if (View::shared('auditeelogin') === 'I') {
        //     $sessionuser  = session('user');
        //     $userchargeid = $sessionuser->userid;
        // } else {
        //     $chargedel    =   session('charge');
        //     $userchargeid   =   $chargedel->userchargeid;
        // }
        $chargedel    =   session('charge');
        $usertypecode   =   $chargedel->usertypecode;

        // Step 4: Insert or Update the file details into the database
        if (View::shared('auditeelogin') === $usertypecode) {
            $sessionuser  = session('user');
            $userchargeid = $sessionuser->userid;
        } else {
            $userchargeid   =   $chargedel->userchargeid;
        }
        if ($fileUploadId) {

            // Update existing record
            DB::table('audit.fileuploaddetail')
                ->where('fileuploadid', $fileUploadId)
                ->update([
                    'filepath' => $filePath,
                    'filename' => $modifiedName,  // Store the modified filename
                    'filesize' => $file->getSize(),
                    'mimetype' => $file->getClientMimeType(),
                    'uploadedby' => $userchargeid,
                    'usertypecode'  =>  $usertypecode,
                    'uploadedon' => now(),
                    'statusflag' => 'Y', // Set status flag to 'Y' (Active)
                ]);
        } else {
            // Step 5: Insert new file details into database
            $fileUploadId = DB::table('audit.fileuploaddetail')->insertGetId([
                'filepath' => $filePath,
                'filename' => $modifiedName,  // Store the modified filename
                'filesize' => $file->getSize(),
                'mimetype' => $file->getClientMimeType(),
                'uploadedby' =>   $userchargeid,
                'usertypecode'  =>  $usertypecode,
                'uploadedon' => now(),
                'statusflag' => 'Y', // Set status flag to 'Y' (Active)
            ], 'fileuploadid');  // Get the ID of the inserted record
        }

        // Step 6: Return the fileupload_id as a response
        $uploadResult = [
            'fileupload_id' => $fileUploadId,
            // other data can be added here
        ];

        return response()->json($uploadResult);  // This will return a JsonResponse
    }

    /**
     * Validate the file (size, type, existence)
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return mixed
     */
    // private function validateFile(UploadedFile $file)
    // {
    //     $validator = Validator::make([
    //         'file' => $file,
    //     ], [
    //         'file' => 'required|file|mimes:jpeg,png,jpg,pdf,xls,xlsx|max:10240'
    //         ,  // Adjust validation rules
    //     ]);

    //     if ($validator->fails()) {
    //         return $validator->errors()->all();
    //     }

    //     // Check if the file already exists
    //     if (Storage::exists($file->getClientOriginalName())) {
    //         return ['File already exists'];
    //     }

    //     return true;
    // }

    // private function compressImage(UploadedFile $file)
    // {
    //     $image = Image::make($file);
    //     $image->save($file->getRealPath(), 75);  // Adjust compression quality (0-100)
    //     return $file;
    // }

    private function storeFile(UploadedFile $file, string $destinationPath, string $modifiedName)
    {
        // Store the file with the modified name (no hyphens)
        return $file->storeAs($destinationPath, $modifiedName, 'public');
// $path = $file->storeAs($destinationPath, $modifiedName, 'custom_uploads');
       // $fullPath = Storage::disk('custom_uploads')->path($path);
        //return $fullPath;
    }




    // public function slipMultipleFileUpload(array $files, string $destinationPath, $auditslipid, $active_fileuploadid, $deactive_fileuploadid, $rejoinderstatus)
    // {
    //     if($rejoinderstatus == 'Y') $rejoinderstatus    =   'Y';
    //     else    $rejoinderstatus    =   null;

    //     $uploadResults = [];  // Array to store uploaded file IDs
    //     $errorMessages = [];  // Array to collect error messages

    //     // Retrieve user and charge details
    //     $chargedel = session('charge');
    //     $usertypecode = $chargedel->usertypecode;

    //     $sessionuser = session('user');
    //     $sessionuserid = $sessionuser->userid;

    //     // echo count($files);

    //     // Step 2: Handle new file uploads
    //     if (count($files) > 0) {
    //         foreach ($files as $file) {

    //             // print_r($file);
    //             // Step 1: Validate the file (Ensure the file is valid before proceeding)
    //             $validationResult = $this->validateFile($file);
    //             if ($validationResult !== true) {
    //                 $errorMessages[] = $validationResult;  // Collect validation errors
    //                 continue;  // Skip this file and move to the next one
    //             }

    //             // Step 2: Modify the filename by removing hyphens
    //             $originalName = $file->getClientOriginalName();
    //             $modifiedName = str_replace('-', '', $originalName);

    //             // Step 3: Store the file
    //             $filePath = $this->storeFile($file, $destinationPath, $modifiedName);

    //             if (empty($filePath)) {
    //                 $errorMessages[] = 'File upload failed for ' . $originalName;
    //                 continue;  // Skip this file and move to the next one
    //             }

    //             $fileUploadId = null;

    //             if ($auditslipid) {
    //                 $slipfileuploaddel = DB::table('audit.slipfileupload')
    //                     ->where('auditslipid', $auditslipid)
    //                     ->where('statusflag', 'N')
    //                     ->first();

    //                 if ($slipfileuploaddel) {
    //                     $fileUploadId = $slipfileuploaddel->fileuploadid;
    //                 }
    //             }

    //             // echo $fileUploadId;

    //             // Step 4: Insert or Update the file details into the database
    //             if ($fileUploadId) {
    //                 // Update existing file upload record
    //                 DB::table('audit.fileuploaddetail')
    //                     ->where('fileuploadid', $fileUploadId)
    //                     ->update([
    //                         'filepath' => $filePath,
    //                         'filename' => $modifiedName,
    //                         'filesize' => $file->getSize(),
    //                         'mimetype' => $file->getClientMimeType(),
    //                         'uploadedby' => $sessionuserid,
    //                         'usertypecode' => $usertypecode,
    //                         'uploadedon' => now(),
    //                         'statusflag' => 'Y',
    //                     ]);

    //                     DB::table('audit.slipfileupload')
    //                     ->where('fileuploadid', $fileUploadId)
    //                     ->where('auditslipid', $auditslipid)
    //                     ->update(['statusflag' => 'Y','rejoinderstatus' =>  $rejoinderstatus]);
    //                 // $uploadResults[] = $fileUploadId;

    //             } else {
    //                 // Insert new file upload record
    //                 $fileUploadId = DB::table('audit.fileuploaddetail')->insertGetId([
    //                     'filepath' => $filePath,
    //                     'filename' => $modifiedName,
    //                     'filesize' => $file->getSize(),
    //                     'mimetype' => $file->getClientMimeType(),
    //                     'uploadedby' => $sessionuserid,
    //                     'usertypecode' => $usertypecode,
    //                     'uploadedon' => now(),
    //                     'statusflag' => 'Y',
    //                 ], 'fileuploadid');
    //             }

    //             // Append the current file's upload ID to the result array
    //             $uploadResults[] = $fileUploadId;
    //         }
    //     }

    //     // Step 3: Return the results and errors
    //     if (!empty($errorMessages)) {
    //         return response()->json([
    //             'error_messages' => $errorMessages,
    //         ], 400);  // Return error messages with a 400 Bad Request status
    //     }

    //     return response()->json([
    //         'uploaded_files' => $uploadResults,
    //     ]);
    // }


    public function insert_slipfileupload($auditslipid, array $fileUploadIds, $rejoinderstatus, $rejoindercycle, $processcode)
    {
        if (!($rejoinderstatus == 'Y')) {
            $rejoinderstatus    =   null;
            $rejoindercycle    =   null;
        }

        // Retrieve session user details
        $sessionuser = session('user');
        $sessionuserid = $sessionuser->userid ?? null;

        if (!$sessionuserid) {
            return response()->json([
                'error' => 'Session user ID is missing.'
            ], 400);
        }

        if (count($fileUploadIds) > 0) {
            foreach ($fileUploadIds as $fileuploadid) {
                if ($fileuploadid) {
                    // Check if an existing record matches auditslipid and fileuploadid
                    $slipfileuploaddel = DB::table('audit.slipfileupload')
                        ->where('auditslipid', $auditslipid)
                        ->where('fileuploadid', $fileuploadid)
                        ->where('statusflag', 'Y')
                        ->first();

                    if ($slipfileuploaddel) {
                    } else {
                        // echo 'else';
                        // Insert a new record
                        DB::table('audit.slipfileupload')->insert([
                            'fileuploadid' => $fileuploadid,
                            'auditslipid' => $auditslipid,
                            'statusflag' => 'Y',
                            'createdby' => $sessionuserid,
                            'rejoinderstatus'   =>  $rejoinderstatus,
                            'rejoindercycle'   =>  $rejoindercycle,
                            'processcode'       =>  $processcode,
                            'createdon' => now(),
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Slip file upload records updated successfully.',
        ]);
    }


    public function deactive_uploadefile($auditslipid, $deactive_fileuploadid)
    {
        $sessionuser = session('user');
        $sessionuserid = $sessionuser->userid;

        if ($deactive_fileuploadid && count($deactive_fileuploadid) > 0) {
            foreach ($deactive_fileuploadid as $id) {
                // Handle fileupload table
                $fileuploaddel = DB::table('audit.fileuploaddetail')
                    ->where('fileuploadid', $id)
                    ->first();

                if ($fileuploaddel) {
                    // Determine status based on the 'createdby' user
                    $status = ($fileuploaddel->uploadedby == $sessionuserid) ? 'N' : 'C';

                    DB::table('audit.fileuploaddetail')
                        // ->where('auditslipid', $auditslipid)
                        ->where('fileuploadid', $id)
                        ->update(['statusflag' => $status]);
                }

                // Handle slipfileupload table
                $slipfileuploaddel = DB::table('audit.slipfileupload')
                    ->where('fileuploadid', $id)
                    ->where('auditslipid', $auditslipid)
                    ->first();

                if ($slipfileuploaddel) {
                    // Determine status based on the 'createdby' user
                    $status = ($slipfileuploaddel->createdby == $sessionuserid) ? 'N' : 'C';

                    DB::table('audit.slipfileupload')
                        ->where('auditslipid', $auditslipid)
                        ->where('fileuploadid', $id)
                        ->update(['statusflag' => $status]);
                }
            }
        }
    }


    private function validateFile(UploadedFile $file)
    {
        $validator = Validator::make([
            'file' => $file,
        ], [
            'file' => 'required|file|mimes:jpeg,png,jpg,pdf,xls,xlsx|max:3072',  // Max size 3MB (3072KB)
        ]);

        // If validation fails, return error messages
        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        // Check if the file already exists
        if (Storage::exists('public/' . $file->getClientOriginalName())) {
            return ['File already exists'];
        }

        return true;
    }

    public function slipMultipleFileUpload(array $files, string $destinationPath, $auditslipid, $active_fileuploadid, $deactive_fileuploadid, $rejoinderstatus, $auditscheduleid)
    {

        // Fetch the relevant data from the database
        $slipfileuploaddel = DB::table('audit.inst_auditschedule as sc')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'mi.deptcode')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('sc.statusflag', 'F')
            ->select('mi.deptcode', 'mi.regioncode', 'mi.distcode', 'mi.instid', 'd.financialyear','ap.auditquartercode')
            ->get();

        //  $uploadfolder = storage_path('app/public'); // Correct base path for storage files
        $uploadfolder = View::shared('uploadsfilefoldername');
        $deptcode = $slipfileuploaddel[0]->deptcode;
        $regioncode = $slipfileuploaddel[0]->regioncode;
        $distcode = $slipfileuploaddel[0]->distcode;
        $instid = $slipfileuploaddel[0]->instid;
        $financialyear = $slipfileuploaddel[0]->financialyear;
        $slipfileuploadpath = View::shared('slipfileuploadpath');

        // Construct the base path and break it into components
        if($slipfileuploaddel[0]->auditquartercode == 'Q1')
        {
            $pathParts = [
            $deptcode,
            $regioncode,
            $distcode,
            $financialyear,
            $instid,
            $slipfileuploadpath
        ];
        }
        else
        {
            $pathParts = [
                $financialyear,
                $slipfileuploaddel[0]->auditquartercode,
            $deptcode,
            $regioncode,
            $distcode,
            $instid,
            $slipfileuploadpath
        ];
        }

        //  app/public/uploads/01/06/569/2025/730/slipauditor

        // Initialize the base path
        $currentPath = $uploadfolder; // Use storage_path here

        // Iterate over the path parts and check if each folder exists
        foreach ($pathParts as $part) {
            $currentPath .= DIRECTORY_SEPARATOR . $part; // Use DIRECTORY_SEPARATOR for compatibility

            // Check if the current part exists, if not, create it
            if (!is_dir($currentPath)) {
                // Folder doesn't exist, create it
                if (!mkdir($currentPath, 0777, true)) {
                    echo "Failed to create folder: $currentPath\n";
                }
            }
        }

        $destinationPath   =   $currentPath;

        if ($rejoinderstatus == 'Y') {
            $rejoinderstatus = 'Y';
        } else {
            $rejoinderstatus = null;
        }

        $uploadResults = [];  // Array to store uploaded file IDs
        $errorMessages = [];  // Array to collect error messages

        // Retrieve user and charge details
        $chargedel = session('charge');
        $usertypecode = $chargedel->usertypecode;

        $sessionuser = session('user');
        $sessionuserid = $sessionuser->userid;

        // Step 2: Handle new file uploads
        if (count($files) > 0) {
            foreach ($files as $file) {
                // Step 1: Validate the file
                $validationResult = $this->validateFile($file);
                if ($validationResult !== true) {
                    $errorMessages[] = $validationResult;  // Collect validation errors
                    continue;  // Skip this file and move to the next one
                }

                // Step 2: Modify the filename by removing hyphens
                $originalName = $file->getClientOriginalName();
                //$modifiedName = str_replace('-', '', $originalName);

                $modifiedName = preg_replace('/[\s\-,]+/', '', $originalName);

                $hashedName = hash('sha256', $modifiedName);

                // Step 3: Get the first 20 characters of the hash to limit the length
                $encryptedName = substr($hashedName, 0, 20);  // Get only the first 20 characters

                // Step 4: Get the original file extension
                $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);

                // Combine the encrypted name with the extension
                $encryptedNameWithExtension = $encryptedName . '.' . $fileExtension;


                // Step 3: Compress the file if necessary (image, PDF, Excel)
                if (in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
                    $file = $this->compressImage($file);  // Compress image files (JPG, PNG)
                } elseif ($file->getMimeType() == 'application/pdf') {
                    $file = $this->compressPdf($file);  // Compress PDF files
                } elseif (in_array($file->getMimeType(), ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                    $file = $this->compressExcel($file);  // Compress Excel files (if necessary)
                }

                // Step 4: Store the file
                $filePath = $this->storeFile($file, $destinationPath, $encryptedNameWithExtension);

                if (empty($filePath)) {
                    $errorMessages[] = 'File upload failed for ' . $originalName;
                    continue;  // Skip this file and move to the next one
                }

                $fileUploadId = null;

                if ($auditslipid) {
                    $slipfileuploaddel = DB::table('audit.slipfileupload')
                        ->where('auditslipid', $auditslipid)
                        ->where('statusflag', 'N')
                        ->first();

                    if ($slipfileuploaddel) {
                        $fileUploadId = $slipfileuploaddel->fileuploadid;
                    }
                }

                // Step 5: Insert or Update the file details into the database
                if ($fileUploadId) {
                    // Update existing file upload record
                    DB::table('audit.fileuploaddetail')
                        ->where('fileuploadid', $fileUploadId)
                        ->update([
                            'filepath' => $filePath,
                            'filename' => $modifiedName,
                            'filesize' => $file->getSize(),
                            'mimetype' => $file->getClientMimeType(),
                            'uploadedby' => $sessionuserid,
                            'usertypecode' => $usertypecode,
                            'uploadedon' => now(),
                            'statusflag' => 'Y',
                        ]);

                    DB::table('audit.slipfileupload')
                        ->where('fileuploadid', $fileUploadId)
                        ->where('auditslipid', $auditslipid)
                        ->update(['statusflag' => 'Y', 'rejoinderstatus' =>  $rejoinderstatus]);
                } else {
                    // Insert new file upload record
                    $fileUploadId = DB::table('audit.fileuploaddetail')->insertGetId([
                        'filepath' => $filePath,
                        'filename' => $modifiedName,
                        'filesize' => $file->getSize(),
                        'mimetype' => $file->getClientMimeType(),
                        'uploadedby' => $sessionuserid,
                        'usertypecode' => $usertypecode,
                        'uploadedon' => now(),
                        'statusflag' => 'Y',
                    ], 'fileuploadid');
                }

                // Append the current file's upload ID to the result array
                $uploadResults[] = $fileUploadId;
            }
        }

        // Step 6: Return the results and errors
        if (!empty($errorMessages)) {
            return response()->json([
                'error_messages' => $errorMessages,
            ], 400);  // Return error messages with a 400 Bad Request status
        }

        return response()->json([
            'uploaded_files' => $uploadResults,
        ]);
    }




    private function compressImage(UploadedFile $file)
    {
        // $image = Image::make($file);
        // $image->save($file->getRealPath(), 75);  // Adjust compression quality (0-100)
        return $file;
    }


    private function compressPdf(UploadedFile $file)
    {
        // You would need to use a library like Ghostscript or a third-party service
        // to compress PDFs. For now, we can just leave it uncompressed.
        return $file;
    }

    private function compressExcel(UploadedFile $file)
    {
        // Excel compression logic goes here if needed.
        // For now, we will leave the Excel files as they are.
        return $file;
    }
}
