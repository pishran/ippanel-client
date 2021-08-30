# IPPanel Client

کلایت پی‌اچ‌پی برای پنل پیامک آی‌پی پنل

```php
$client = new \Pishran\IpPanel\Client('YOUR_API_KEY');
```

## نیازمندی‌ها
جهت استفاده از این پکیج به موارد زیر نیاز خواهید داشت:
- PHP >= 7.0
- ext-curl
- ext-json

## روش نصب

برای نصب و استفاده از این پکیج می‌توانید از کمپوسر استفاده کنید:

`composer require pishran/ippanel-client`

## متدها و نحوه استفاده

### دریافت موجودی اعتبار
```php
$credit = $client->getCredit();
```

### ارسال پیامک
```php
$originator = '5000012345'; // شماره فرستنده
$recipients = ['09123456789', '09111111111']; // شماره‌های گیرنده
$message = 'Hello world!'; // متن پیام

$bulkId = $client->sendMessage($originator, $recipients, $message);
```

### دریافت اطلاعات پیام
```php
$message = $client->getMessage($bulkId);

echo $message->status;
echo $message->cost;
echo $message->sentAt;
```

### دریافت وضعیت تحویل پیام
```php
[$statuses, $paginationInfo] = $client->fetchStatuses($bulkId);

foreach ($statuses as $status) {
    echo "Recipient: $status->recipient, Status: $status->status";
}

echo "Total: $paginationInfo->total";
```

### دریافت پیام‌های ورودی
```php
[$messages, $paginationInfo] = $client->fetchInbox();

foreach ($messages as $message) {
    echo "Received message $message->message from number $message->sender in line $message->number";
}
```

### ایجاد الگوی پیام‌های پرتکرار
```php
$pattern = $client->createPattern('Your otp is %code%.');

echo $pattern->code; // شناسه الگو
```

### ارسال پیام با استفاده از الگو
```php
$patternCode = '12eb1cbb'; // شناسه الگو
$originator = '5000012345'; // شماره فرستنده
$recipient = '09123456789'; // شماره گیرنده
$values = ['code' => 12345];

$bulkId = $client->sendPattern($patternCode, $originator, $recipient, $values);
```

## مدیریت خطا
```php
try {
    $credit = $client->getCredit();
} catch (Exception $e) {
    echo $e->getCode(); // کد خطا
    echo $e->getMessage(); // متن توضیح خطا
}
``` 
