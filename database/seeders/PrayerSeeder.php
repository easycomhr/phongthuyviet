<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Prayer;
use App\Models\PrayerCategory;
use Illuminate\Database\Seeder;
use RuntimeException;

class PrayerSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PrayerCategorySeeder::class);

        $categoryIdBySlug = PrayerCategory::query()
            ->pluck('id', 'slug')
            ->all();

        $prayers = [
            [
                'category_slug' => 'gia-tien',
                'title' => 'Văn khấn gia tiên ngày thường',
                'slug' => 'van-khan-gia-tien-ngay-thuong',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy chư vị Tổ tiên nội ngoại họ [Họ tộc].\nTín chủ con là: [Họ tên], ngụ tại: [Địa chỉ].\nHôm nay ngày [Ngày tháng năm], chúng con thành tâm dâng hương hoa lễ vật, kính mời gia tiên về trước án thụ hưởng.\nCúi xin gia tiên chứng giám lòng thành, phù hộ độ trì cho toàn gia được bình an, hiếu thuận, mọi việc hanh thông.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'mung-1-ngay-ram',
                'title' => 'Văn khấn mùng 1 và ngày rằm',
                'slug' => 'van-khan-mung-mot-ngay-ram',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy Hoàng Thiên Hậu Thổ, chư vị Tôn thần.\nTín chủ con là: [Họ tên], ngụ tại: [Địa chỉ].\nHôm nay là ngày [Mùng 1/Rằm] tháng [Tháng], chúng con sửa biện hương hoa lễ vật, dâng lên trước án.\nKính xin chư vị chứng giám, phù hộ cho gia đạo an khang, thân tâm an ổn, mọi việc cát tường.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'nhap-trach',
                'title' => 'Văn khấn nhập trạch nhà mới',
                'slug' => 'van-khan-nhap-trach-nha-moi',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy Hoàng Thiên Hậu Thổ chư vị Tôn thần.\nCon kính lạy ngài Bản gia Táo Quân, ngài Thổ Công, Long Mạch Tôn thần.\nTín chủ con là: [Họ tên], cùng gia quyến ngụ tại: [Địa chỉ cũ].\nHôm nay ngày [Ngày tháng năm], nhân chọn được ngày lành tháng tốt, chúng con làm lễ nhập trạch vào ở tại: [Địa chỉ mới].\nKính cáo chư vị tôn thần cho phép gia quyến chúng con được chính thức cư ngụ, lập bát hương, an vị gia tiên.\nCúi xin chứng giám và phù hộ cho gia đạo bình an, nhân khang vật thịnh, mọi việc thuận thành.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'khai-truong',
                'title' => 'Văn khấn khai trương cửa hàng',
                'slug' => 'van-khan-khai-truong-cua-hang',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy quan Đương niên Hành khiển, quan Bản cảnh Thành Hoàng, chư vị Thần linh cai quản khu vực này.\nCon kính lạy ngài Thần Tài tiền vị, ngài Thổ Địa phúc đức chính thần.\nTín chủ con là: [Họ tên], đại diện cơ sở: [Tên cửa hàng/doanh nghiệp].\nHôm nay ngày [Ngày tháng năm], chúng con thành tâm sắm sửa hương hoa lễ vật, làm lễ khai trương tại: [Địa chỉ].\nKính xin chư vị chứng giám, phù hộ cho việc kinh doanh được hanh thông, khách hàng tín nhiệm, tài lộc tăng trưởng.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'dong-tho',
                'title' => 'Văn khấn động thổ xây dựng',
                'slug' => 'van-khan-dong-tho-xay-dung',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy Hoàng Thiên Hậu Thổ chư vị Tôn thần.\nCon kính lạy ngài Bản xứ Thần linh, ngài Bản gia Táo Quân, ngài Thổ Địa, Long Mạch Tôn thần.\nTín chủ con là: [Họ tên], ngụ tại: [Địa chỉ].\nHôm nay ngày [Ngày tháng năm], chọn được giờ lành, chúng con làm lễ động thổ khởi công tại: [Địa điểm công trình].\nKính xin chư vị chứng giám, cho phép động thổ và phù hộ công trình bền vững, thợ thuyền bình an, mọi việc thuận lợi.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'ong-cong-ong-tao',
                'title' => 'Văn khấn cúng ông Công ông Táo',
                'slug' => 'van-khan-cung-ong-cong-ong-tao-23-thang-chap',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy Đông Trù Tư Mệnh Táo Phủ Thần Quân.\nTín chủ con là: [Họ tên], ngụ tại: [Địa chỉ].\nHôm nay ngày 23 tháng Chạp, chúng con thành tâm sửa soạn lễ vật, dâng hương kính tiễn Táo quân chầu trời.\nKính xin ngài chứng giám, bẩm báo điều lành, phù hộ cho gia đình năm mới bình an, thuận hòa, tài lộc hanh thông.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'giao-thua',
                'title' => 'Văn khấn giao thừa ngoài trời',
                'slug' => 'van-khan-giao-thua-ngoai-troi',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy chư vị Hành khiển, Phán quan năm cũ và năm mới.\nTín chủ con là: [Họ tên], ngụ tại: [Địa chỉ].\nGiờ phút giao thừa, chúng con thành tâm dâng hương lễ ngoài trời, kính tiễn cựu niên, nghênh tân niên.\nKính xin chư vị chứng giám, ban phúc lộc, độ trì gia quyến một năm mới bình an, cát khánh.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'tat-nien',
                'title' => 'Văn khấn tất niên cuối năm',
                'slug' => 'van-khan-tat-nien-cuoi-nam',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy chư vị Thần linh, gia tiên tiền tổ.\nTín chủ con là: [Họ tên], ngụ tại: [Địa chỉ].\nHôm nay ngày [Ngày tháng năm], nhân lễ tất niên, chúng con sửa soạn hương hoa lễ vật cùng mâm cơm kính cáo tạ ơn.\nKính xin chư vị chứng giám, phù hộ cho năm mới gia đạo hòa thuận, sức khỏe dồi dào, công việc thuận thành.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'le-chua',
                'title' => 'Văn khấn lễ Phật tại chùa',
                'slug' => 'van-khan-le-phat-tai-chua',
                'content' => "Nam mô Bổn Sư Thích Ca Mâu Ni Phật! (3 lần)\nCon kính lạy mười phương chư Phật, chư vị Bồ Tát, Hiền Thánh Tăng.\nHôm nay con tên là: [Họ tên], ngụ tại: [Địa chỉ], đến cửa chùa dâng hương lễ Phật.\nNguyện xin Tam Bảo gia hộ cho thân tâm an lạc, trí tuệ sáng suốt, biết làm điều lành, tránh điều dữ.\nNguyện hồi hướng công đức cho gia đình được bình an, mọi việc tốt lành.\nNam mô Bổn Sư Thích Ca Mâu Ni Phật! (3 lần)",
            ],
            [
                'category_slug' => 'than-tai-tho-dia',
                'title' => 'Văn khấn Thần Tài - Thổ Địa',
                'slug' => 'van-khan-than-tai-tho-dia-hang-ngay',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy ngài Thần Tài tiền vị, ngài Thổ Địa phúc đức chính thần.\nTín chủ con là: [Họ tên], đại diện cơ sở: [Tên cửa hàng/doanh nghiệp].\nHôm nay ngày [Ngày tháng năm], chúng con thành tâm dâng hương trà quả, kính cầu tài lộc hanh thông.\nKính xin các ngài chứng giám, phù hộ việc làm ăn thuận lợi, buôn may bán đắt, gia đạo bình an.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'thoi-noi',
                'title' => 'Văn khấn thôi nôi cho bé',
                'slug' => 'van-khan-thoi-noi-cho-be',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy mười hai Bà Mụ, ba Đức Ông.\nHôm nay là ngày thôi nôi của cháu: [Tên em bé], sinh ngày [Ngày tháng năm].\nGia đình chúng con thành tâm dâng lễ tạ ơn chư vị đã che chở cho cháu suốt năm đầu đời.\nKính xin tiếp tục phù hộ cháu hay ăn chóng lớn, mạnh khỏe, thông minh, hiếu thảo.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'day-thang',
                'title' => 'Văn khấn đầy tháng',
                'slug' => 'van-khan-day-thang-em-be',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy mười hai Bà Mụ, ba Đức Ông.\nHôm nay là ngày đầy tháng của cháu: [Tên em bé], sinh ngày [Ngày tháng năm].\nGia đình chúng con thành tâm dâng lễ tạ ơn, kính xin chư vị chứng giám.\nCúi xin tiếp tục che chở, phù hộ cháu bình an, khỏe mạnh, lớn lên thuận hòa.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'cuoi-hoi',
                'title' => 'Văn khấn lễ gia tiên ngày cưới',
                'slug' => 'van-khan-gia-tien-ngay-cuoi',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy Tổ tiên nội ngoại họ [Họ tộc].\nHôm nay ngày [Ngày tháng năm], gia đình chúng con cử hành hôn lễ cho: [Tên chú rể] và [Tên cô dâu].\nChúng con thành tâm dâng hương hoa lễ vật, kính cáo trước gia tiên, cúi xin chứng giám và chúc phúc cho đôi tân hôn hòa hợp, thủy chung, trăm năm hạnh phúc.\nKính xin gia tiên phù hộ cho hai bên gia đình thuận hòa, trên dưới đồng lòng.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'gio-chap',
                'title' => 'Văn khấn ngày giỗ',
                'slug' => 'van-khan-ngay-gio-gia-tien',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy chư vị Tổ tiên nội ngoại họ [Họ tộc].\nHôm nay là ngày húy kỵ của [Quan hệ - Họ tên người quá cố], con cháu chúng con thành tâm sửa soạn hương hoa lễ vật dâng lên trước án.\nKính mời hương linh và gia tiên về thụ hưởng lễ vật, chứng giám lòng thành.\nCúi xin phù hộ con cháu bình an, hiếu thuận, gia đạo hòa mục, làm ăn thuận lợi.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'xuat-hanh',
                'title' => 'Văn khấn trước khi xuất hành',
                'slug' => 'van-khan-truoc-khi-xuat-hanh',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy chư vị Thần linh cai quản lộ trình, chư vị gia tiên tiền tổ.\nTín chủ con là: [Họ tên], hôm nay ngày [Ngày tháng năm], có việc xuất hành đi [Nơi đến].\nCon thành tâm thắp nén hương thơm, kính xin chư vị phù hộ độ trì đi đường bình an, gặp dữ hóa lành, mọi việc hanh thông.\nNguyện cho chuyến đi thuận lợi, công việc thành tựu, trở về bình an.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'hieu-le-tang-nghi',
                'title' => 'Văn khấn dùng trong hiếu lễ - tang nghi',
                'slug' => 'van-khan-hieu-le-tang-nghi',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy chư vị Tôn thần bản cảnh, kính lạy hương linh: [Họ tên người quá cố].\nHôm nay ngày [Ngày tháng năm], gia quyến chúng con thành tâm sắm sửa lễ nghi theo tục lệ hiếu tang, cúi xin chư vị chứng giám.\nNguyện cầu hương linh sớm siêu sinh tịnh cảnh, gia quyến giữ trọn hiếu đạo, trên dưới thuận hòa, mọi việc lo toan được hanh thông.\nNam mô A Di Đà Phật! (3 lần)",
            ],
            [
                'category_slug' => 'ta-mo-thanh-minh',
                'title' => 'Văn khấn tạ mộ tiết Thanh Minh',
                'slug' => 'van-khan-ta-mo-tiet-thanh-minh',
                'content' => "Nam mô A Di Đà Phật! (3 lần)\nCon kính lạy thần linh bản thổ, kính lạy hương linh gia tiên nội ngoại.\nHôm nay nhân tiết Thanh Minh, con cháu chúng con về phần mộ dâng hương hoa lễ vật, tu sửa phần mộ và tỏ lòng tưởng niệm.\nKính xin chư vị chứng giám lòng thành, phù hộ độ trì cho con cháu được bình an, hiếu thuận và thuận hòa.\nNam mô A Di Đà Phật! (3 lần)",
            ],
        ];

        foreach ($prayers as $prayer) {
            $categoryId = $categoryIdBySlug[$prayer['category_slug']] ?? null;
            if (!$categoryId) {
                throw new RuntimeException(sprintf('Missing prayer category slug: %s', $prayer['category_slug']));
            }

            Prayer::updateOrCreate(
                ['slug' => $prayer['slug']],
                [
                    'category_id' => $categoryId,
                    'title' => $prayer['title'],
                    'content' => $prayer['content'],
                ],
            );
        }
    }
}
