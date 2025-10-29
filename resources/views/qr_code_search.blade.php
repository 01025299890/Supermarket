<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>قراءة QR للمنتج</title>
</head>

<body>
    <h2>ابحث عن منتج من QR</h2>
    <input type="file" id="qrImage" accept="image/*">
    <canvas id="canvas" hidden></canvas>
    <p id="result"></p>

    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <script>
        document.getElementById('qrImage').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function () {
                const img = new Image();
                img.onload = function () {
                    const canvas = document.getElementById('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0, img.width, img.height);

                    const imageData = ctx.getImageData(0, 0, img.width, img.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code) {
                        document.getElementById('result').innerText = "الكود المقروء: " + code.data;

                        // إرسال الكود إلى Laravel للبحث عن المنتج
                        fetch(`/api/search-by-qr?code=${code.data}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.product) {
                                    document.getElementById('result').innerText +=
                                        `\nالمنتج: ${data.product.name} - السعر: ${data.product.price}`;
                                } else {
                                    document.getElementById('result').innerText += "\n❌ المنتج غير موجود";
                                }
                            });
                    } else {
                        document.getElementById('result').innerText = "لم يتم التعرف على أي QR في الصورة 😕";
                    }
                };
                img.src = reader.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>

</html>