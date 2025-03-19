<style type="text/css">
    .select2-container{
        width:  100% !important;
    }
</style>
<form action="{{ url('pms/product-management/sub-category/'.$subcategory->id.'/update-attributes') }}" method="post" accept-charset="utf-8">
@csrf
    <h5>{{ $subcategory->name }} Attributes</h5>
    <hr>
    <div class="form-group">
        <label for="attributes">Attributes:</label>
        <div class="input-group input-group-md mb-3 d-">
            <select name="productAttributes[]" id="attributes" class="form-control rounded select2" multiple data-placeholder="Choose Attributes" onchange="updateAttributes()">
                @if(isset($attributes[0]))
                @foreach($attributes as $key => $attribute)
                    <option value="{{ $attribute->id }}" {{ in_array($attribute->id, isset($categoryAttributes[0]) ? $categoryAttributes : []) ? 'selected' : '' }}>{{ $attribute->name }}</attribute>
                @endforeach
                @endif
            </select>
        </div> 
    </div>
    <div class="form-group row">
    @if(isset($attributes[0]))
    @foreach($attributes as $key => $attribute)
        <div class="col-md-6 mb-3 attributes attribute-{{ $attribute->id }}" style="display: none">
            <label for="attribute-{{ $attribute->id }}">{{ $attribute->name }}:</label>
            <div class="input-group input-group-md mb-3 d-">
                <select name="attributeOptions[{{ $attribute->id }}][]" id="attribute-{{ $attribute->id }}" class="form-control rounded select2" multiple>
                    @if(isset($attribute->options[0]))
                    @foreach($attribute->options as $key => $option)
                        <option value="{{ $option->id }}" {{ in_array($option->id, isset($categoryAttributeOptions) ? $categoryAttributeOptions : []) ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                    @endif
                </select>
            </div> 
        </div>
    @endforeach
    @endif
    </div>
    <button type="submit" class="btn btn-md btn-success"><i class="la la-edit"></i>&nbsp;Update Sub Category Attributes</button>
</form>

<script type="text/javascript">
    $('.select2').select2();
    updateAttributes();
</script>