<?php

namespace App\Services;

class TemplateInterpolator
{
    public function interpolate(array $parameters, array $payload)
    {
        $interpolated = [];

        foreach ($parameters as $index => $parameter) {
            if (is_array($parameter)) {
                // if the parameters is a array[] we are going to callback(recursive)
                $interpolated[$index] = $this->interpolate($parameter, $payload);
            } else {
                $interpolated[$index] = $this->interpolateValue($parameter, $payload);
            }
        }

        return $interpolated;
        // $interpolated = [
        //     'to' => 'admin@flowhub.com',
        //     'subject' => 'Nuevo issue: Error en la base de datos', 
        //     'options' => [
        //         'urgent' => true, 
        //         'tag' => 'open'   
        //     ]
        // ];
    }

    public function interpolateValue($value, $payload)
    {
        if (!is_string($value)) {
            return $value;
        }

        if (preg_match('/^\{\{\s*([^}]+)\s*\}\}$/', trim($value), $isolatedMatch)) {
            $searcher = data_get($payload, $isolatedMatch[1]);
            
            if ($searcher === null) {
                throw new \Exception('Value not found in payload: ' . $isolatedMatch[1]);
            }
            
            return $searcher; 
        }

        // the algorithm find the content between {{ and }}
        // we create matches to find {{ ... }}
        return preg_replace_callback('/\{\{\s*([^}]+)\s*\}\}/', function ($matches) use ($payload) {

            // we are going to use matches[1] = (payload.user.login) because matches[0] 
            // contains the whole text(El usuario comento: {{payload.issue.body}})
            $searcher = data_get($payload, $matches[1]);

            if ($searcher === null) {
                throw new \Exception('Value not found in payload: ' . $matches[1]);
            }

            return (string) $searcher;
        }, $value);
    }

}
// parameter example
// parameters = [
//     'to' => 'admin@flowhub.com',
//     'subject' => 'Nuevo issue: {{trigger.issue.title}}',
//     'options' => [
//         'urgent' => true,
//         'tag' => '{{trigger.issue.state}}'
//     ]
// ];

// {
//   "trigger": {
//     "action": "opened",
//     "issue": {
//       "url": "https://api.github.com/repos/jrodriguezes/FlowHub/issues/42",
//       "id": 123456789,
//       "number": 42,
//       "title": "El sistema se cae al intentar hacer login",
//       "state": "open",
//       "locked": false,
//       "comments": 0,
//       "created_at": "2026-08-09T18:00:00Z",
//       "body": "Cuando le doy clic al botón rojo, me tira un error 500 en la consola.",
//       "user": {
//         "login": "rachelbarquero",
//         "id": 987654,
//         "avatar_url": "https://avatars.githubusercontent.com/u/987654?v=4",
//         "type": "User",
//         "site_admin": false
//       },
//       "labels": [
//         {
//           "id": 223344,
//           "name": "bug",
//           "color": "d73a4a"
//         },
//         {
//           "id": 556677,
//           "name": "urgente",
//           "color": "ff0000"
//         }
//       ]
//     },
//     "repository": {
//       "id": 444555666,
//       "name": "FlowHub",
//       "full_name": "jrodriguezes/FlowHub",
//       "private": true,
//       "html_url": "https://github.com/jrodriguezes/FlowHub",
//       "description": "Plataforma de automatización UTN",
//       "language": "PHP",
//       "owner": {
//         "login": "jrodriguezes",
//         "type": "User"
//       }
//     },
//     "sender": {
//       "login": "rachelbarquero"
//     }
//   }
// }
