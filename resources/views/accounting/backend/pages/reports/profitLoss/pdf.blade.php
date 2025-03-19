<table>
  <tr>
    <td>
      <table>
        <tr>
          <th style="width: 75%">
              <h5><strong>{{ isset($expense->id) ? $expense->name : '' }}</strong></h5>
          </th>
          <th style="width: 25%" class="text-right">
              <h5><strong>Amount</strong></h5>
          </th>
        </tr>
        {!! $expenses !!}
      </table>
    </td>
    <td>
      <table>
        <tr>
          <th style="width: 75%">
              <h5><strong>{{ isset($income->id) ? $income->name : '' }}</strong></h5>
          </th>
          <th style="width: 25%" class="text-right">
              <h5><strong>Amount</strong></h5>
          </th>
        </tr>
        {!! $incomes !!}
      </table>
    </td>
  </tr>
</table>