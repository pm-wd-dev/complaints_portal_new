<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('qrCode')->get();
        return view('admin.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
           // 'address' => 'required|string|max:255',
        ]);

        $location = Location::create($validatedData);

        // Generate QR Code
        $this->generateQRCode($location);

        return redirect()->route('admin.locations')->with('success', 'Location created successfully');
    }

    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
          
        ]);

        $location->update($validatedData);

        // Update QR Code
        if ($location->qrCode) {
            $location->qrCode->delete();
            $this->generateQRCode($location);
        }
        else
        {
            $this->generateQRCode($location);

        }

        return redirect()->route('admin.locations')->with('success', 'Location updated successfully');
    }

    public function destroy(Location $location)
    {
        // QR code will be automatically deleted due to cascade
        $location->delete();
        return redirect()->route('admin.locations')->with('success', 'Location deleted successfully');
    }

    protected function generateQRCode(Location $location)
    {
        // Generate a URL with location data
        $url = route('public.complaints.create', ['location' => $location->id]);

        // Generate QR code image
        $qrCode = new QrCode($url);
        $qrCode->setSize(300)
               ->setErrorCorrectionLevel(ErrorCorrectionLevel::High);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $qrPath = public_path('qrcode');
        if (!file_exists($qrPath)) {
            mkdir($qrPath, 0755, true);
        }
        // Save QR code image
        $filename = 'qrcode/' . Str::slug($location->name) . '-' . $location->id . '.png';
        $filePath = public_path($filename);
        file_put_contents($filePath, $result->getString());

        // Create attachment record
        Attachment::create([
            'location_id' => $location->id,
            'file_path' => $filename,
            'file_type' => 'png',
            'description' => 'QR Code for ' . $location->name
        ]);
    }
}
