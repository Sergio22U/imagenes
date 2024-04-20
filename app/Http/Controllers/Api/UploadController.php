<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max size as needed
        ]);

        // Guardar la imagen en el almacenamiento
        $imagePath = $request->file('image')->store('uploads', 'public');

        // Crear una nueva instancia de Image y guardarla en la base de datos
        $image = new Image();
        $image->title = $request->title;
        $image->description = $request->description;
        $image->image_path = $imagePath;
        $image->save();

        return response()->json(['message' => 'Imagen subida correctamente'], 200);
    }

    public function getImage()
    {
        // Obtener la última imagen subida desde la base de datos
        $image = Image::latest()->first();
    
        if ($image) {
            // Obtener la ruta completa de la imagen
            $imagePath = storage_path('app/public/' . $image->image_path);
            
            // Verificar si el archivo existe en el almacenamiento público
            if (file_exists($imagePath)) {
                // Obtener el tipo MIME del archivo
                $mimeType = mime_content_type($imagePath);
                
                // Leer el contenido del archivo
                $fileContent = file_get_contents($imagePath);
                
                // Crear un array con el contenido del archivo y el título
                $imageData = [
                    'image' => base64_encode($fileContent), // Codificar el contenido en base64 para transferencia segura
                    'title' => $image->title
                ];
                
                // Devolver el contenido del archivo y el título como JSON con el tipo de contenido adecuado
                return response()->json($imageData)->header('Content-Type', $mimeType);
            } else {
                // Si la imagen no existe, devolver un error 404
                return response()->json(['message' => 'No se encontró ninguna imagen'], 404);
            }
        } else {
            return response()->json(['message' => 'No se encontró ninguna imagen'], 404);
        }
    }
}
