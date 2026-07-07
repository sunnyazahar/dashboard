<?php
$filePath = '/Applications/XAMPP/xamppfiles/htdocs/laravel/resources/views/Stock/edit.blade.php';
$content = file_get_contents($filePath);

// Normalize newlines
$normalized = str_replace("\r\n", "\n", $content);

$pattern = '/<td class="text-center">\s*<input type="checkbox"\s*class="pkg-is-dgr"\s*name="packages\[\{\{\s*\$index\s*\}\}\]\[is_dgr\]"[^>]*>.*?<\/tr>\s*<tr class="dgr-sub-row"\s*data-index="\{\{\s*\$index\s*\}\}"\s*style="\{\{\s*\$pkg->is_dgr \? \'\' : \'display: none;\'\s*\}\}">\s*<td colspan="2"><\/td>\s*<td colspan="7">/s';

$replacement = '<td class="text-center"><input type="checkbox"
                                                                          class="pkg-is-irregular" name="packages[{{ $index }}][is_delivery_irregularity]"
                                                                          {{ $pkg->is_delivery_irregularity ? \'checked\' : \'\' }}></td>
                                                                  <td class="text-center"><input type="checkbox"
                                                                          class="pkg-is-dgr" name="packages[{{ $index }}][is_dgr]"
                                                                          {{ $pkg->is_dgr ? \'checked\' : \'\' }}></td>
                                                                  <td class="text-center"><input type="checkbox"
                                                                          name="packages[{{ $index }}][is_not_stackable]" {{ $pkg->is_not_stackable ? \'checked\' : \'\' }}></td>
                                                                  <td class="text-center"><input type="checkbox"
                                                                          name="packages[{{ $index }}][is_medicine]" {{ $pkg->is_medicine ? \'checked\' : \'\' }}></td>
                                                                  <td class="text-center"><input type="checkbox"
                                                                          name="packages[{{ $index }}][is_xray]" {{ $pkg->is_xray ? \'checked\' : \'\' }}></td>
                                                                  <td class="text-center">
                                                                      <button type="button"
                                                                          class="btn btn-link text-primary p-0 btn-copy-row"><i
                                                                              class="icofont icofont-copy-alt"></i></button>
                                                                  </td>
                                                                  <td class="text-center">
                                                                      <button type="button"
                                                                          class="btn btn-link text-danger p-0 btn-remove-row"><i
                                                                              class="icofont icofont-trash"></i></button>
                                                                  </td>
                                                              </tr>
                                                              <tr class="irregularity-sub-row" data-index="{{ $index }}"
                                                                  style="{{ $pkg->is_delivery_irregularity ? \'\' : \'display: none;\' }}">
                                                                  <td colspan="2"></td>
                                                                  <td colspan="12">
                                                                      <div class="dgr-container" style="background: #fff9e6; border: 1px solid #ffeeba;">
                                                                          <i class="icofont icofont-warning dgr-warning-icon" style="color: #f0ad4e;"></i>
                                                                          <div class="dgr-field" style="flex: 1;">
                                                                              <label class="field-label">Delivery irregularities</label>
                                                                              <select class="form-control select2-irregularities" name="packages[{{ $index }}][delivery_irregularities][]" multiple="multiple">
                                                                                  <option value="Damaged packaging - no repacking required" {{ in_array(\'Damaged packaging - no repacking required\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Damaged packaging - no repacking required</option>
                                                                                  <option value="Damaged packaging - repacking required" {{ in_array(\'Damaged packaging - repacking required\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Damaged packaging - repacking required</option>
                                                                                  <option value="Missing DG label / marking on package" {{ in_array(\'Missing DG label / marking on package\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Missing DG label / marking on package</option>
                                                                                  <option value="Missing documentation - Commercial invoice / Packing list" {{ in_array(\'Missing documentation - Commercial invoice / Packing list\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Missing documentation - Commercial invoice / Packing list</option>
                                                                                  <option value="Missing documentation - DG" {{ in_array(\'Missing documentation - DG\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Missing documentation - DG</option>
                                                                                  <option value="Missing documentation - Other" {{ in_array(\'Missing documentation - Other\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Missing documentation - Other</option>
                                                                                  <option value="Missing label on packaging" {{ in_array(\'Missing label on packaging\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Missing label on packaging</option>
                                                                                  <option value="Packaging not fit for airfreight" {{ in_array(\'Packaging not fit for airfreight\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Packaging not fit for airfreight</option>
                                                                                  <option value="Packaging not fumigated" {{ in_array(\'Packaging not fumigated\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Packaging not fumigated</option>
                                                                                  <option value="Packaging not heat treated" {{ in_array(\'Packaging not heat treated\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Packaging not heat treated</option>
                                                                                  <option value="Vessel Name / PO Number not mentioned on packaging (label)" {{ in_array(\'Vessel Name / PO Number not mentioned on packaging (label)\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Vessel Name / PO Number not mentioned on packaging (label)</option>
                                                                                  <option value="Vessel Name / PO Number not mentioned on supplier documentation" {{ in_array(\'Vessel Name / PO Number not mentioned on supplier documentation\', (array)($pkg->delivery_irregularities ?? [])) ? \'selected\' : \'\' }}>Vessel Name / PO Number not mentioned on supplier documentation</option>
                                                                              </select>
                                                                          </div>
                                                                      </div>
                                                                  </td>
                                                              </tr>
                                                              <tr class="dgr-sub-row" data-index="{{ $index }}"
                                                                  style="{{ $pkg->is_dgr ? \'\' : \'display: none;\' }}">
                                                                  <td colspan="2"></td>
                                                                  <td colspan="12">';

$newContent = preg_replace($pattern, $replacement, $normalized);

if ($newContent !== null && $newContent !== $normalized) {
    // Also update empty row colspan
    $oldEmptyRow = '<tr class="empty-row">
                                                                  <td colspan="14" class="text-center py-4 text-muted">No';
    $newEmptyRow = '<tr class="empty-row">
                                                                  <td colspan="15" class="text-center py-4 text-muted">No';
    $newContent = str_replace($oldEmptyRow, $newEmptyRow, $newContent);

    file_put_contents($filePath, $newContent);
    echo "SUCCESS";
} else {
    echo "REGEX FAILED";
}
