<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Services;

class AdmsRequestParser
{
    public function parseAttendanceLogs(string $body): array
    {
        $logs = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($body));

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $fields = explode("\t", $line);

            // Format: PIN, DateTime, Status, VerifyType, WorkCode, Reserved1, Reserved2
            if (count($fields) >= 4) {
                $logs[] = [
                    'pin' => trim($fields[0] ?? ''),
                    'punched_at' => trim($fields[1] ?? ''),
                    'status' => (int) trim($fields[2] ?? 0),
                    'verify_type' => (int) trim($fields[3] ?? 0),
                    'work_code' => isset($fields[4]) ? (int) trim($fields[4]) : null,
                    'reserved_1' => isset($fields[5]) ? trim($fields[5]) : null,
                    'reserved_2' => isset($fields[6]) ? trim($fields[6]) : null,
                    'raw' => $line,
                ];
            }
        }

        return $logs;
    }

    public function parseOperationLogs(string $body): array
    {
        $operations = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($body));

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            if (str_starts_with($line, 'USER')) {
                $operations[] = $this->parseUserLine($line);
            } elseif (str_starts_with($line, 'FP')) {
                $operations[] = $this->parseFingerprintLine($line);
            } elseif (str_starts_with($line, 'FACE')) {
                $operations[] = $this->parseFaceLine($line);
            }
        }

        return $operations;
    }

    protected function parseUserLine(string $line): array
    {
        // Format: USER PIN=1 Name=John Card=1234 Pri=0 Passwd= Grp=1 ...
        preg_match_all('/(\w+)=([^\t]*)/', $line, $matches, PREG_SET_ORDER);

        $data = ['type' => 'user'];

        foreach ($matches as $match) {
            $key = strtolower($match[1]);
            $data[$key] = $match[2];
        }

        return $data;
    }

    protected function parseFingerprintLine(string $line): array
    {
        // Format: FP PIN=1 FID=0 Size=1024 Valid=1 TMP=base64data
        preg_match_all('/(\w+)=([^\t]*)/', $line, $matches, PREG_SET_ORDER);

        $data = ['type' => 'fingerprint'];

        foreach ($matches as $match) {
            $key = strtolower($match[1]);
            $data[$key] = $match[2];
        }

        return $data;
    }

    protected function parseFaceLine(string $line): array
    {
        // Format: FACE PIN=1 FID=0 SIZE=1024 VALID=1 TMP=base64data
        preg_match_all('/(\w+)=([^\t]*)/', $line, $matches, PREG_SET_ORDER);

        $data = ['type' => 'face'];

        foreach ($matches as $match) {
            $key = strtolower($match[1]);
            $data[$key] = $match[2];
        }

        return $data;
    }

    public function parseOptions(string $body): array
    {
        $options = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($body));

        foreach ($lines as $line) {
            if (empty(trim($line)) || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $options[trim($key)] = trim($value);
        }

        return $options;
    }
}
