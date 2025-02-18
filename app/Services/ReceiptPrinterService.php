<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Exception;

class ReceiptPrinterService
{
    protected $printerIp;
    protected $printerPort;

    public function __construct()
    {
        // Retrieve from config
        $this->printerIp = config('printer.ip');
        $this->printerPort = config('printer.port');
    }

    public function printReceipt($order)
    {
        try {
            // Connect to the network printer using values from .env
            $connector = new NetworkPrintConnector($this->printerIp, $this->printerPort);
            $printer = new Printer($connector);

            // Print Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Street Food Stall\n");
            $printer->text("Order Receipt\n");
            $printer->text("------------------------------\n");

            // Print Order Details
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            foreach ($order['items'] as $item) {
                $printer->text($item['name'] . " x " . $item['quantity'] . "  $" . number_format($item['price'], 2) . "\n");
            }

            // Print Total
            $printer->text("------------------------------\n");
            $printer->text("Total: $" . number_format($order['total'], 2) . "\n");
            $printer->text("------------------------------\n");

            // Print Footer
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for your order!\n");

            // Cut the receipt
            $printer->cut();

            // Close the printer connection
            $printer->close();

            return "Receipt printed successfully!";
        } catch (Exception $e) {
            return "Error printing receipt: " . $e->getMessage();
        }
    }
}
