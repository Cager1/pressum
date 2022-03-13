<?php

namespace App\Traits;

use App\Mail\IdentityCredentials;
use App\Models\ResourceFile;
use App\Models\User;
use App\Services\EduId\EduIDHelper;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mail;
use mikehaertl\pdftk\Pdf;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

trait HasEduId
{
    public function syncFromLdap()
    {
        return User::withoutEvents(function () {
            $data = EduIDHelper::getLdapData($this->uid);
            return $this->update($data);
        });
    }

    public function changePassword($password = null)
    {
        if (!$password)
            $password = mb_strtoupper(Str::random(8));

        EduIDHelper::changePassword($this, $password);

        return [
            'uid' => $this->uid,
            'branch' => $this->branch,
            'password' => $password
        ];
    }

    public function emailCredentials($email = null, $credentials)
    {
        if ($email) {
            $this->email_private = $email;
            $this->save();
        }

        $data = [
            'name' => $this->full_name,
            'organization' => $this->organizationUnit->name,
            'eduid' => $this->uid . '@' . $this->branch,
            'password' => $credentials['password']
        ];

        Mail::to($this->email_private)->send(new IdentityCredentials($data, 'emails.eduid'));
    }

    public static function createFromLdap($uid) {
        $ldapData = EduIDHelper::getLdapData($uid);

        $laravelUser = User::withoutEvents(function () use ($ldapData) {
            try {
                return User::updateOrCreate(
                    [
                        'uid' => $ldapData['uid']
                    ],
                    $ldapData
                );
            } catch (QueryException $e) {
                User::where('jmbg', $ldapData['jmbg'])
                    ->whereNull('avatar_id')
                    ->delete();
                return User::updateOrCreate(
                    [
                        'uid' => $ldapData['uid']
                    ],
                    $ldapData
                );
            }
        });

        return $laravelUser;
    }

    public function printCredentials($credentials)
    {
        $timestamp = now()->timestamp;
        $fileName = Str::ascii("{$this->first_name}_{$this->last_name}_$timestamp");
        $fileNameUncompressed = 'password_resets/' . $fileName . 'uncompressed.pdf';
        $filePathUncompressed = Storage::path($fileNameUncompressed);

        $pdf = new Pdf(Storage::path('templates/eduid_issue_template.pdf'));

        $data = [
            'institution' => $this->organizationUnit->name,
            'full_name' => $this->full_name,
            'full_name1' => $this->full_name,
            'username' => $this->uid . '@' . $this->branch,
            'password' => $credentials['password']
        ];

        $result = $pdf->fillForm($data, 'UTF-8', false)
            ->flatten()
            ->saveAs($filePathUncompressed);

        if ($result === false) {
            return $pdf->getError();
        }

        // Compress
        $fileNameCompressed = str_replace('uncompressed', '', $fileNameUncompressed);

        $process = new Process(['pdftk', $filePathUncompressed, 'output', Storage::path($fileNameCompressed), 'compress']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $resourceFile = ResourceFile::create([
            'name' => $fileName . '.pdf',
            'folder' => 'password_resets',
            'mimetype' => 'application/pdf',
            'filepath' => $fileName . '.pdf'
        ]);

        Storage::delete($fileNameUncompressed);

        return $resourceFile;
    }
}
