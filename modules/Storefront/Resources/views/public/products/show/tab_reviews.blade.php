@if (setting('reviews_enabled'))
    <div id="reviews" class="tab-pane reviews">
        <div class="row">
            <div class="col-xl-9 col-lg-18">
                @if ($product->purchasedByUser())
                    <div class="review-form-wrap">
                        <form @submit.prevent="addNewReview" @input="errors.clear($event.target.name)">
                            <div class="review-form">
                                <div class="title">{{ trans('storefront::product.add_a_review') }}</div>

                                @honeypot

                                <div class="row">
                                    <div class="col-md-18">
                                        <div class="form-group">
                                            <label>{{ trans('storefront::product.review_form.your_rating') }}<span>*</span></label>

                                            <div class="rating-input">
                                                <input type="radio" name="rating" x-model="reviewForm.rating" id="star-5" value="5">
                                                <label for="star-5" title="5 star">
                                                    <i class="las la-star"></i>
                                                </label>

                                                <input type="radio" name="rating" x-model="reviewForm.rating" id="star-4" value="4">
                                                <label for="star-4" title="4 star">
                                                    <i class="las la-star"></i>
                                                </label>

                                                <input type="radio" name="rating" x-model="reviewForm.rating" id="star-3" value="3">
                                                <label for="star-3" title="3 star">
                                                    <i class="las la-star"></i>
                                                </label>

                                                <input type="radio" name="rating" x-model="reviewForm.rating" id="star-2" value="2">
                                                <label for="star-2" title="2 star">
                                                    <i class="las la-star"></i>
                                                </label>

                                                <input type="radio" name="rating" x-model="reviewForm.rating" id="star-1" value="1">
                                                <label for="star-1" title="1 star">
                                                    <i class="las la-star"></i>
                                                </label>
                                            </div>

                                            <template x-if="errors.has('rating')">
                                                <span class="error-message" x-text="errors.get('rating')"></span>
                                            </template>
                                        </div>

                                        <div class="form-group">
                                            <label for="name">
                                                {{ trans('storefront::product.review_form.name') }}<span>*</span>
                                            </label>
                                            
                                            <input
                                                type="text"
                                                name="reviewer_name"
                                                autocomplete="on"
                                                id="name"
                                                class="form-control"
                                                x-model="reviewForm.reviewer_name"
                                            >
                                            
                                            <template x-if="errors.has('reviewer_name')">
                                                <span class="error-message" x-text="errors.get('reviewer_name')"></span>
                                            </template>
                                        </div>

                                        <div class="form-group">
                                            <label for="comment">
                                                {{ trans('storefront::product.review_form.comment') }}<span>*</span>
                                            </label>
                                            
                                            <textarea rows="5" name="comment" x-model="reviewForm.comment" id="comment" class="form-control"></textarea>
                                            
                                            <template x-if="errors.has('comment')">
                                                <span class="error-message" x-text="errors.get('comment')"></span>
                                            </template>
                                        </div>

                                        @if (setting('google_recaptcha_enabled'))
                                            <div class="form-group p-t-5 captcha-field">
                                                <div class="g-recaptcha" data-sitekey="{{ setting('google_recaptcha_site_key') }}"></div>

                                                <template x-if="errors.has('g-recaptcha-response')">
                                                    <span class="error-message" x-text="errors.get('g-recaptcha-response')"></span>
                                                </template>
                                            </div>
                                        @endif

                                        <button
                                            type="submit"
                                            class="btn btn-primary btn-submit"
                                            :class="{ 'btn-loading': addingNewReview }"
                                        >
                                            {{ trans('storefront::product.review_form.submit_review') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="col-xl-9 col-lg-18">
                <div class="user-review-wrap" :class="{ loading: fetchingReviews }">
                    <template x-if="emptyReviews">
                        <div class="empty-message">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 500 500"
                                preserveAspectRatio="xMidYMid meet">
                                <path d="M226.53,300a10.1,10.1,0,1,0,3,7.14,10.15,10.15,0,0,0-3-7.14Zm0,0"/>
                                <path d="M219.32,280.5a10,10,0,0,1-3.19-.43c-4.41-1.4-5-5.74-5.3-9.09v-.11c-2.54-28.07-4.18-56.14-6.71-84.21-.18-2-.34-4.12.73-5.94a10.13,10.13,0,0,1,3.83-4c3.16-1.9,6.65-3.83,10.59-3.84s7.25,1.8,10.35,3.61a10.13,10.13,0,0,1,4.6,4.89,9.21,9.21,0,0,1,.37,4.66c-2.56,28.56-4.24,57.11-6.8,85.66a11.09,11.09,0,0,1-1.53,5.57C224.87,279.27,222.1,280.42,219.32,280.5Z"/>
                                <path d="M382.92,118.81l-168.17,0q-60.94,0-121.87,0-10.23,0-20.48,0h-24c-22.18,0-41.58,19-41.62,40.93q-.15,88.1,0,176.18c0,20.66,16.7,39.23,37.25,40.86,11.79.94,23.71.41,35.56.53,1.71,0,3.42,0,5.47,0v69l1.16.58c1.06-1.22,2-2.53,3.2-3.68,20.6-20.7,41.27-41.35,61.8-62.13l.16-.16a14.24,14.24,0,0,1,2.42-2,11.17,11.17,0,0,1,6.3-1.67q111.21.15,222.44.06c27.06,0,45.13-18.1,45.13-45.19v-41q0-63.81,0-127.63C427.66,137.19,409.34,118.81,382.92,118.81Zm24.23,213c0,16-9,25-25.1,25q-103.18,0-206.35-.06-12,0-24.06,0c-.32,0-.62,0-.93,0a11.18,11.18,0,0,0-8,3.7c-12,12.26-24.24,24.34-37.23,37.34V357.08c-1.8-.08-3.36-.21-4.93-.21q-24.69,0-49.37,0c-14.85,0-24-9.28-24-24.18q0-84.72,0-169.46c0-14.62,9.25-23.93,23.81-23.93H383.26c14.44,0,23.88,9.48,23.89,24Z"/>
                                <path d="M493.34,97.83c0-26.34-18.3-44.72-44.72-44.72l-168.17,0q-83.2,0-166.39,0c-22.17,0-41.58,19-41.62,40.93,0,8.25,0,16.5,0,24.76q0,10.23,0,20.46h20.5V97.52c0-14.62,9.25-23.93,23.81-23.93H449c14.44,0,23.88,9.48,23.89,24V266.14c0,16-9,25-25.1,25h-40.6v20.48h41c27.06,0,45.13-18.1,45.13-45.19Q493.32,182.14,493.34,97.83ZM153.75,379c.43-.49.86-1,1.32-1.44q10.31-10.36,20.63-20.71-12,0-24.06,0c-.32,0-.62,0-.93,0v23.81l.62.31Z"/>
                            </svg>

                            <span>{{ trans('storefront::product.be_the_first_one_to_review_this_product') }}</span>
                        </div>
                    </template>

                    <template x-if="!emptyReviews">
                        <template x-for="(review, index) in reviews.data" :key="index">
                            <div class="user-review">
                                <div class="user-review-header">
                                    <div class="user-avatar" x-text="review.reviewer_name.substr(0, 1)"></div>
    
                                    <div class="user-review-top">
                                        <h6 class="user-name" x-text="review.reviewer_name"></h6>
                                        
                                        @include('storefront::public.partials.product_rating', [
                                            'data' => 'review'
                                        ])
                                    </div>
                                </div>
                                
                                <p class="reviewer-message" x-text="review.comment"></p>

                                <template x-if="review.is_approved">
                                    <span class="review-date" x-text="review.created_at_formatted"></span>
                                </template>
                                
                                <template x-if="!review.is_approved">
                                    <span class="review-approval">
                                        {{ trans("review::messages.submitted_for_approval") }}
                                    </span>
                                </template>
                            </div>
                        </template>
                    </template>
                </div>
                
                <template x-if="totalReviews > 5">
                    @include('storefront::public.partials.pagination')
                </template>
            </div>
        </div>
    </div>
@endif

@push('scripts')
    @if (setting('google_recaptcha_enabled'))
        <script async src="https://www.google.com/recaptcha/api.js"></script>
    @endif
@endpush
