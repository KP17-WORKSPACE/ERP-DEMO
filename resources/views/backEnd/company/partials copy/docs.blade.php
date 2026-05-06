<div class="card">
    <div class="card-body">

        <h5 class="mb-3">Documentation</h5>

        <div class="row gy-3">

            {{-- Upload Documents --}}
            <div class="col-lg-4">
                <label class="form-label mb-1">Upload Documents</label>
                <input type="file" class="form-control form-control-sm"
                       multiple
                       @change="onFile($event, 'docs')">
                <small class="text-danger">@{{ errors.docs }}</small>
            </div>

            {{-- Existing Docs (Edit Mode) --}}
            <div class="col-12" v-if="form.docs_preview && form.docs_preview.length">
                <div class="mt-3">
                    <h6>Uploaded Documents:</h6>

                    <ul class="small">
                        <li v-for="doc in form.docs_preview">@{{ doc }}</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
</div>
