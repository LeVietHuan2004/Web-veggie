@extends('layouts.client')

@section('title', 'Dịch vụ')

@section('breadcrumb', 'Dịch vụ')

@section('content')

    <!-- ABOUT US AREA START -->
    <div class="ltn__about-us-area pb-115">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 align-self-center">
                    <div class="about-us-img-wrap ltn__img-shape-left  about-img-left">
                        <img src="{{ asset('assets/clients/img/service/11.jpg') }}" alt="Image">
                    </div>
                </div>
                <div class="col-lg-7 align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color">// DỊCH VỤ ĐÁNG TIN CẬY</h6>
                            <h1 class="section-title">Chúng tôi là những người
                                chuyên nghiệp & giàu kinh nghiệm<span>.</span></h1>
                            <p>Chúng tôi luôn cam kết mang đến dịch vụ tốt nhất, đáp ứng mọi nhu cầu của khách hàng một cách
                                nhanh chóng và hiệu quả.</p>
                        </div>
                        <div class="about-us-info-wrap-inner about-us-info-devide">
                            <p>Với đội ngũ nhân viên tận tâm và trang thiết bị hiện đại, chúng tôi tự tin cung cấp các sản
                                phẩm và dịch vụ chất lượng cao, đảm bảo sự hài lòng tối đa cho khách hàng. Mỗi bước trong
                                quy trình đều được thực hiện cẩn thận và chuyên nghiệp.</p>
                            <div class="list-item-with-icon">
                                <ul>
                                    <li><a href="javascript:void(0)">Giao hàng tận nơi 24/7 miễn phí</a></li>
                                    <li><a href="javascript:void(0)">Đội ngũ chuyên gia giàu kinh nghiệm</a></li>
                                    <li><a href="javascript:void(0)">Thiết bị hiện đại, đảm bảo an toàn</a></li>
                                    <li><a href="javascript:void(0)">Hàng hóa phong phú, đa dạng</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ABOUT US AREA END -->

    <!-- SERVICE AREA START (Service 1) -->
    <div class="ltn__service-area section-bg-1 pt-115 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title white-color---">Dịch Vụ Của Chúng Tôi</h1>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/1.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Rau củ hữu cơ</a></h3>
                            <p>Chúng tôi cung cấp các loại rau củ sạch, không hóa chất, đảm bảo an toàn cho sức khỏe gia
                                đình bạn.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/2.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Giao hàng tận nơi</a></h3>
                            <p>Dịch vụ giao hàng nhanh chóng, đúng hẹn, đảm bảo rau củ luôn tươi ngon khi đến tay khách
                                hàng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/3.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Canh tác tự nhiên</a></h3>
                            <p>Rau củ được trồng theo phương pháp hữu cơ, không sử dụng thuốc trừ sâu hay phân bón hóa học.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/3.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Hỗ trợ khách hàng</a></h3>
                            <p>Đội ngũ tư vấn luôn sẵn sàng hỗ trợ bạn trong việc lựa chọn sản phẩm phù hợp và giải đáp thắc
                                mắc.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/1.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Đóng gói an toàn</a></h3>
                            <p>Sản phẩm được đóng gói kỹ lưỡng, sạch sẽ, giữ nguyên độ tươi ngon và giá trị dinh dưỡng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/2.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Sản phẩm đa dạng</a></h3>
                            <p>Chúng tôi cung cấp nhiều loại rau củ theo mùa, phù hợp cho mọi nhu cầu nấu nướng hằng ngày.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SERVICE AREA END -->

    <!-- OUR JOURNEY AREA START -->
    <div class="ltn__our-journey-area bg-image bg-overlay-theme-90 pt-280 pb-350 mb-35 plr--9" data-bg="img/bg/8.jpg') }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__our-journey-wrap ">
                        <ul>
                            <li><span class="ltn__journey-icon">1990</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}" alt="Khởi đầu">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Khởi đầu hành trình</h3>
                                                <p>Trang trại đầu tiên được hình thành tại vùng quê thanh bình, với mục tiêu
                                                    trồng rau sạch phục vụ cộng đồng địa phương.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="active"><span class="ltn__journey-icon">2000</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}" alt="Mở rộng">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Mở rộng canh tác</h3>
                                                <p>Diện tích canh tác tăng gấp đôi, đồng thời áp dụng phương pháp trồng hữu
                                                    cơ nhằm đảm bảo chất lượng sản phẩm.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li><span class="ltn__journey-icon">2010</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}" alt="Thành tựu">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Được chứng nhận hữu cơ</h3>
                                                <p>Trang trại nhận được chứng nhận sản xuất hữu cơ từ tổ chức uy tín, đánh
                                                    dấu bước tiến lớn trong cam kết với sức khỏe người tiêu dùng.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li><span class="ltn__journey-icon">2018</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}" alt="Thương hiệu">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Ra mắt thương hiệu riêng</h3>
                                                <p>Thành lập thương hiệu rau củ sạch, mở rộng hệ thống phân phối tại các
                                                    siêu thị và cửa hàng tiện lợi.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li><span class="ltn__journey-icon">2024</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/history-1.jpg') }}" alt="Chuyển đổi số">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Chuyển đổi số</h3>
                                                <p>Phát triển hệ thống bán hàng trực tuyến, giao hàng tận nơi, nâng cao trải
                                                    nghiệm khách hàng trong thời đại công nghệ số.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OUR JOURNEY AREA END -->

@endsection
