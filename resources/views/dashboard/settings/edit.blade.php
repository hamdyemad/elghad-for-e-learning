@extends('layouts.master')

@section('title', 'إعدادات الموقع')

@section('content')
<x-breadcrumb 
    title="إعدادات الموقع"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'إعدادات الموقع']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">تعديل الإعدادات العامة للموقع</h4>
                
                <form action="{{ route('dashboard.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-alert type="success" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="facebook"><i class="mdi mdi-facebook text-primary"></i> Facebook Link</label>
                                <input type="url" name="facebook" id="facebook" class="form-control" 
                                       value="{{ old('facebook', $settings->facebook) }}" placeholder="https://facebook.com/...">
                                @error('facebook') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="instagram"><i class="mdi mdi-instagram text-danger"></i> Instagram Link</label>
                                <input type="url" name="instagram" id="instagram" class="form-control" 
                                       value="{{ old('instagram', $settings->instagram) }}" placeholder="https://instagram.com/...">
                                @error('instagram') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="tiktok"><i class="mdi mdi-music-note text-dark"></i> TikTok Link</label>
                                <input type="url" name="tiktok" id="tiktok" class="form-control" 
                                       value="{{ old('tiktok', $settings->tiktok) }}" placeholder="https://tiktok.com/@...">
                                @error('tiktok') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-form-input
                                name="mobile_number"
                                label="رقم الجوال / Mobile Number"
                                type="tel"
                                :value="old('mobile_number', $settings->mobile_number)"
                                placeholder="+500115205478"
                            />
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <x-form-textarea
                            name="terms_of_use"
                            label="شروط الاستخدام (Terms of Use)"
                            :rich="true"
                            height="300px"
                            :value="old('terms_of_use', $settings->terms_of_use)"
                        />
                    </div>

                    <div class="form-group mb-4">
                        <x-form-textarea
                            name="privacy_policy"
                            label="سياسة الخصوصية (Privacy Policy)"
                            :rich="true"
                            height="300px"
                            :value="old('privacy_policy', $settings->privacy_policy)"
                        />
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="mdi mdi-check-all"></i> حفظ الإعدادات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
