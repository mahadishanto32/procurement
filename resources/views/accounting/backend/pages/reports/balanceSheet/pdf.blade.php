<table>
  <tr>
    <td>
      <table>
        <tr>
            <th style="width: 75%">
                <h5><strong>{{ isset($asset->id) ? $asset->name : '' }}</strong></h5>
            </th>
            <th style="width: 25%" class="text-right">
                <h5><strong>Amount</strong></h5>
            </th>
        </tr>
        {!! $assets !!}
      </table>
    </td>
    <td>
      <table>
        <tr>
          <tr>
              <th style="width: 75%">
                  <h5><strong>{{ isset($liability->id) ? $liability->name : '' }}</strong></h5>
              </th>
              <th style="width: 25%" class="text-right">
                  <h5><strong>Amount</strong></h5>
              </th>
          </tr>
          {!! $liabilities !!}
        </tr>
      </table>
    </td>
  </tr>
</table>